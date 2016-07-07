<?php

namespace Dende\SoccerBot;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Telegram\Bot\Api as TelegramApi;
use Telegram\Bot\Objects\Update as TelegramUpdate;
use Finite\StateMachine\StateMachine as FiniteStateMachine;
use Carbon\Carbon;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Dende\SoccerBot\Model\Team;
use Dende\SoccerBot\Model\TeamQuery;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Model\MatchQuery;
use Dende\SoccerBot\Model\PrivateChat;
use Dende\SoccerBot\Model\PrivateChatQuery;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\GroupChatQuery;

/**
 * Class SoccerBot
 */
class SoccerBot
{
	protected $config;
	protected $api_config;
	protected $rootData;
	/** @var  \Monolog\Logger */
	protected $log;
	/** @var  \GuzzleHttp\Client */
	protected $client;
	protected $header;
	protected $offset;
	/** @var \Telegram\Bot\Api */
	protected $telegram;
	/** @var  \Finite\Loader\ArrayLoader */
	protected $privateChatLoader;
	/** @var  \Finite\Loader\ArrayLoader */
	protected $groupChatLoader;
	/** @var  \Symfony\Component\Translation\Translator */
	protected $lang;
	protected $states;

	function init(){
		$this->initConfig();
		$this->initTranslation();
		$this->initLog();
		$this->initTelegramApi();
		$this->initFooballDataApi();
		$this->initDb();
		$this->initStates();
	}

	function run(){
		while (true){
			$this->updateMatches();
			$updates = $this->telegram->getUpdates([
                'offset' => $this->offset,
                'limit' => array_get($this->api_config, 'TELEGRAM_API_LIMIT'),
                'timeout' => array_get($this->api_config, 'TELEGRAM_API_TIMEOUT')
            ]);
			foreach ($updates as $update){
				$this->handle($update);
			}
		}
	}

	function handle(TelegramUpdate $update){
		$this->offset = $update->getUpdateId() + 1;
		$message = $update->getMessage();
		if(!$message){
			return;
		}
		$chatId = $message->getChat()->getId();
		$chat = null;

		try{
			$chat = GroupChatQuery::create()->findOneByChatId($chatId);
		} catch (\Propel\Runtime\Exception\EntityNotFoundException $e){}

		if (is_null($chat)) {
			try {
				$chat = PrivateChatQuery::create()->findOneByChatId($chatId);
			} catch (\Propel\Runtime\Exception\EntityNotFoundException $e) {}
		}

		if (is_null($chat)) {
			$chat = $this->newChat($update);
		}

		$this->handleChat($chat, $update);
	}

	private function handleChat($chat, $update)
		/** @var PrivateChat|GroupChat $chat */
		/** @var TelegramUpdate $update */
	{
		$chatId = $chat->getChatId();
		$fsm = null;

		if ($chat instanceof PrivateChat){
			if (array_key_exists($chatId, $this->states)){
				$fsm = array_get($this->states, 'private.' . $chatId);
			} else {
				$fsm = new FiniteStateMachine($chat);
				$this->privateChatLoader->load($fsm);
				$fsm->initialize();
				$this->initPrivateFSM($fsm);
				$this->states['private'][$chatId] = $fsm;
			}
			$this->handlePrivateChat($chat, $update, $fsm);
		} else if ($chat instanceof GroupChat){
			if (array_key_exists($chatId, $this->states)){
                $fsm = array_get($this->states, 'group.' . $chatId);
            } else {
				$fsm = new FiniteStateMachine($chat);
				$this->groupChatLoader->load($fsm);
				$fsm->initialize();
				$this->initGroupFSM($fsm);
                array_set($this->states, 'group.' . $chatId, $fsm);
				$this->states['group'][$chatId] = $fsm;
			}
			$this->handleGroupChat($chat, $update, $fsm);
		}
	}


	private function handlePrivateChat(PrivateChat $chat, TelegramUpdate $update, FiniteStateMachine $fsm)
	{
		$this->log->info("Handling private update");
		$message = $update->getMessage();
		if (is_null($message)){
			return;
		}
		$newChatParticipant = $message->getNewChatParticipant();
		if (!is_null($newChatParticipant)) {
			if ($newChatParticipant->getUsername() == $this->api_config['TELEGRAM_API_USERNAME']){
				//we got added to a group
				return;
			}
		}

		$text = $message->getText();
		if (is_null($text)){
			return;
		}

		$entities = array_get($message->getRawResponse(), 'entities');
		if (is_null($entities)){
			return;
		}

		foreach ($entities as $entity){
			if (array_get($entity, 'type') == 'bot_command'){
				//command found
				$command = substr($text, array_get($entity, 'offset'), array_get($entity, 'length'));
				$args = explode(' ', substr($text, array_get($entity, 'length')+1));
				if (substr($command,0,1) != '/'){
					$this->log->warn("Wrong type of Command");
					return;
				}
				$command = substr($command,1);

				if (str_contains($command, '@')){
					$parts  = explode('@', $command);
					if (count($parts) != 2){
						$this->log->warn("Wrong type of Command");
						return;
					}
					if ($parts[1] != $this->api_config['TELEGRAM_API_USERNAME']){
						$this->log->warn("Wrong Username");
						return;
					}
					$command = $parts[0];
				}

				try {
					$state = $fsm->getCurrentState();
					$this->log->info("applying transition $command from state $state to {$fsm->getCurrentState()}");
					$fsm->apply($command, ['chat' => $chat, 'args' => $args]);
					$this->log->info("applied transition $command from state $state to {$fsm->getCurrentState()}");
				} catch (\Finite\Exception\StateException $e){
					$this->log->warn($e->getMessage());
				} catch (\Finite\Exception\TransitionException $e){
					$this->log->warn($e->getMessage());
				} finally {
					if ($fsm->getCurrentState() == "muted"){
						$this->log->info("Overriding mute");
						if ($command == "next"){
							$this->nextCommand($chat, $args);
						} else if ($command == "info"){
							$this->infoCommand($chat);
						} else if ($command == "curr"){
							$this->currCommand($chat);
						}
					}
				}
			}
		}

	}

	private function handleGroupChat(GroupChat $chat, TelegramUpdate $update, FiniteStateMachine $fsm)
	{
		$this->log->info("Handling Group update");
		$message = $update->getMessage();
		if (is_null($message)){
			return;
		}

		$newChatParticipant = $message->getNewChatParticipant();
		if (!is_null($newChatParticipant)) {
			if ($newChatParticipant->getUsername() == $this->api_config['TELEGRAM_API_USERNAME']){
				//we got added to a group
				return;
			}
		}

		$text = $message->getText();
		if (is_null($text)){
			return;
		}

		$entities = array_get($message->getRawResponse(), 'entities');
		if (is_null($entities)){
			return;
		}

		foreach ($entities as $entity){
			if (array_get($entity, 'type') == 'bot_command'){
				//command found
				$command = substr($text, array_get($entity, 'offset'), array_get($entity, 'length'));
				$args = explode(' ', substr($text, array_get($entity, 'length')+1));
				if (substr($command,0,1) != '/'){
					$this->log->warn("Wrong type of Command");
					return;
				}
				$command = substr($command,1);
				
				if (str_contains($command, '@')){
					$parts  = explode('@', $command);
					if (count($parts) != 2){
						$this->log->warn("Wrong type of Command");
						return;
					}
					if ($parts[1] != $this->api_config['TELEGRAM_API_USERNAME']){
						$this->log->warn("Wrong Username");
						return;
					}
					$command = $parts[0];
				}

				try {
					$state = $fsm->getCurrentState();
					$this->log->info("applying transition $command from state $state to {$fsm->getCurrentState()}");
					$fsm->apply($command, ['chat' => $chat, 'args' => $args]);
					$this->log->info("applied transition $command from state $state to {$fsm->getCurrentState()}");
				} catch (\Finite\Exception\StateException $e){
					$this->log->warn($e->getMessage());
				} catch (\Finite\Exception\TransitionException $e){
					$this->log->warn($e->getMessage());
				} finally {
					if ($fsm->getCurrentState() == "muted"){
						$this->log->info("Overriding mute");
						if ($command == "next"){
							$this->nextCommand($chat, $args);
						} else if ($command == "info"){
							$this->infoCommand($chat);
						} else if ($command == "curr"){
							$this->currCommand($chat);
						}
					}
				}
			}
		}
	}

	function updateMatches(){
		$this->log->info("Updating matches");
		/** @var Match[] $timedMatches */
		$timedMatches = MatchQuery::create()
			->where('matches.status = ?', 'TIMED')
			->_or()
			->where('matches.status = ?', 'IN_PLAY')
			->find();


		foreach ($timedMatches as $match){
			$date = Carbon::instance($match->getDate());
			$diff = Carbon::now()->diffInMinutes($date, false);

			if ($diff <= 0 && $diff >= -200){
				//these games started less than 200 minutes ago
				try {
					$newData = $this->updateMatch($match);
				} catch (\GuzzleHttp\Exception\ServerException $e){
					//football-api.org 500ed
					continue;
				} catch (\GuzzleHttp\Exception\BadResponseException $e){
					//some other bad response
					continue;
				} catch (\Exception $e){
					//can this even happen?
					continue;
				}

				if (!is_null($newData)){
					$this->liveticker($match, $newData);
				}
			}
		}
	}

	function error($message){
		$this->log->error($message);
		throw new \Exception($message);
	}

	function newChat(TelegramUpdate $update) {
		$chatType = $update->getMessage()->getChat()->getType();

		switch ($chatType){
			case "group":
			case "supergroup":
			case "channel":
			default:
				$chat = new GroupChat();
				break;
			case "private":
				$chat = new PrivateChat();
				break;
		}

		$chat->setChatId($update->getMessage()->getChat()->getId());
		$chat->setType($chatType);
		$chat->setState($chat::$initialState);
		$chat->save();
		return $chat;
	}

	private function initDb()
	{
		if (is_null(TeamQuery::create()->findPk(1))){
            $uri = array_get($this->rootData, '_links.teams.href');
			if (empty($uri)){
				$this->error('No Teams found');
			}
			$response = $this->client->get($uri, $this->header);
			$data = json_decode($response->getBody()->getContents(), true);
			foreach (array_get($data, 'teams', []) as $teamData){
				$team = new Team();
				$team->setName(array_get($teamData, 'name'));
				$team->setCode(array_get($teamData, 'code'));
				$team->save();
			}
			$this->log->info('Updated the Teams in the database');
		} else {
			$this->log->info('No updates for the teams needed');
		}

		$matchCount = MatchQuery::create()->count();

		if ($matchCount < $this->rootData["numberOfGames"]){
            $uri = array_get($this->rootData, '_links.fixtures.href');
			if (empty($uri)){
				$this->error('No Fixtures found');
			}
			$response = $this->client->get($uri, $this->header);
			$data = json_decode($response->getBody()->getContents(), true);
			foreach ($data["fixtures"] as $fixtureData){
				$match = new Match();
				$homeTeam = TeamQuery::create()->findOneByName($fixtureData["homeTeamName"]);
				if (is_null($homeTeam)){
					$this->error('Home Team is null');
				}
				$match->setHomeTeam($homeTeam);
				$awayTeam = TeamQuery::create()->findOneByName($fixtureData["awayTeamName"]);
				if (is_null($awayTeam)){
					$this->error('Away Team is null');
				}
				$match->setAwayTeam($awayTeam);
				$date = new \DateTime($fixtureData["date"]);
				$date->setTimezone(new \DateTimeZone('Europe/Berlin'));
				$match->setDate($date);
				$match->setStatus($fixtureData["status"]);
				$match->setUrl(array_get($fixtureData, '_links.self.href'));
				$match->save();
			}
			$this->log->info('Updated the Matches in the database');
		} else {
			$this->log->info('No updates for the matches needed');
		}
	}

	private function initLog()
	{
		$this->log = new Logger('name');
		foreach ($this->config['log']['streamhandlers'] as $streamhandler){
			$this->log->pushHandler(new StreamHandler($streamhandler));
		}
		$this->log->info('Initializing the EM2016TippBot');
	}

	private function initTelegramApi()
	{
		$this->offset = 0;
		$this->telegram = new TelegramApi($this->api_config['TELEGRAM_API_TOKEN']);
	}

	private function initFooballDataApi()
	{
		$this->client = new \GuzzleHttp\Client();
		$this->header = array('headers' => array('X-Auth-Token' => $this->api_config['FOOTBALL_DATA_API_TOKEN']));
		$response = $this->client->get($this->api_config['FOOTBALL_DATA_ROOT_URL'], $this->header);
		$this->rootData = json_decode($response->getBody()->getContents(), true);
	}

	private function initStates()
	{
		$this->states = ['private' => [], 'group' => []];
		$this->privateChatLoader = new \Finite\Loader\ArrayLoader($this->config['FSM_CHAT']);
		$this->groupChatLoader   = new \Finite\Loader\ArrayLoader($this->config['FSM_GROUPCHAT']);
	}

	private function updateMatch(Match $match){
		$newData = [];
		$uri = $match->getUrl();
		$response = $this->client->get($uri, $this->header);
		$data = json_decode($response->getBody()->getContents(), true);
		$matchData = $data["fixture"];

		$oldStatus        = $match->getStatus();
		$newStatus        = array_get($matchData, 'status');
		$oldHomeTeamGoals = $match->getHomeTeamGoals();
		$newHomeTeamGoals = array_get($matchData, 'result.goalsHomeTeam');
		$oldAwayTeamGoals = $match->getAwayTeamGoals();
		$newAwayTeamGoals = array_get($matchData, 'result.goalsAwayTeam');

		if ($oldStatus != $newStatus){
			$newData['status'] = $newStatus;
			$match->setStatus($newStatus);
		}
		if ($oldHomeTeamGoals != $newHomeTeamGoals){
			$newData['homeTeamGoalsScored'] = $newHomeTeamGoals - $oldHomeTeamGoals;
			$match->setHomeTeamGoals($newHomeTeamGoals);
		}
		if ($oldAwayTeamGoals != $newAwayTeamGoals){
			$newData['awayTeamGoalsScored'] = $newAwayTeamGoals - $oldAwayTeamGoals;
			$match->setAwayTeamGoals($newAwayTeamGoals);
		}

		$match->save();

		return (empty($newData)?null:$newData);
	}

	private function liveticker(Match $match, $newData) {
		$fsms = array_merge($this->states['private'], $this->states['group']);
		foreach ($fsms as $fsm){
			/** @var FiniteStateMachine $fsm */
			if ($fsm->getCurrentState() == 'liveticker'){
				$message = "";

				$homeTeam = $match->getHomeTeam();
				$awayTeam = $match->getAwayTeam();

				/** @var GroupChat $chat */
				$chat = $fsm->getObject();

				if (array_get($newData, 'status') == 'IN_PLAY'){
					$message .= $this->lang->trans(
                        'live.matchStarted',
                        [
                            '%homeTeamName%'  => $homeTeam->getName(),
                            '%homeTeamEmoji%' => $homeTeam->getEmoji(),
                            '%awayTeamName%'  => $awayTeam->getName(),
                            '%awayTeamEmoji%' => $awayTeam->getEmoji()
                        ]
                    );
				}

				if (array_has($newData, 'homeTeamGoalsScored')){
                    $goalsScored         = array_get($newData, 'homeTeamGoalsScored');
                    $teamScoredName      = $homeTeam->getName();
                    $teamConcededName    = $awayTeam->getName();
				} else if (array_has($newData, 'awayTeamGoalsScored')){
                    $goalsScored         = array_get($newData, 'awayTeamGoalsScored');
                    $teamScoredName      = $awayTeam->getName();
                    $teamConcededName    = $homeTeam->getName();
                }

                if (!empty($goalsScored)){
                    /** @noinspection PhpUndefinedVariableInspection */
                    $message .= $this->lang->transChoice(
                        'live.teamScored',
                        $goalsScored,
                        [
                            '%teamScoredName%'   => $teamScoredName,
                            '%teamConcededName%' => $teamConcededName,
                            '%goals%'            => $goalsScored
                        ]
                    );
                    $message .= $this->lang->trans(
                        'live.newScore',
                        [
                            '%homeTeamEmoji%' => $homeTeam->getEmoji(),
                            '%homeTeamGoals%' => $match->getHomeTeamGoals(),
                            '%awayTeamGoals%' => $match->getAwayTeamGoals(),
                            '%awayTeamEmoji%' => $awayTeam->getEmoji()
                        ]
                    );
                }

				if (array_get($newData, 'status') == 'FINISHED'){
                    $message .= $this->lang->trans(
                        'live.finalScore',
                        [
                            '%homeTeamName%' => $homeTeam->getName(),
                            '%awayTeamName%' => $awayTeam->getName(),
                            '%homeTeamEmoji%' => $homeTeam->getEmoji(),
                            '%homeTeamGoals%' => $match->getHomeTeamGoals(),
                            '%awayTeamGoals%' => $match->getAwayTeamGoals(),
                            '%awayTeamEmoji%' => $awayTeam->getEmoji()
                        ]
                    );
				}
				$this->sendMessage($message, $chat);
			}
		}
	}

	private function sendMessage($message, $chat){
		/** @var PrivateChat|GroupChat $chat */
		$this->telegram->sendMessage(['chat_id' => $chat->getChatId(), 'text' => $message, 'parse_mode' => 'Markdown']);
	}

	private function initGroupFSM(FiniteStateMachine $fsm)
	{
		$fsm->getDispatcher()->addListener('finite.post_transition.live', [$this, 'groupChatLiveTransition']);
		$fsm->getDispatcher()->addListener('finite.post_transition.mute', [$this, 'groupChatMuteTransition']);
		$fsm->getDispatcher()->addListener('finite.post_transition.info', [$this, 'groupChatInfoTransition']);
		$fsm->getDispatcher()->addListener('finite.post_transition.curr', [$this, 'groupChatCurrTransition']);
		$fsm->getDispatcher()->addListener('finite.post_transition.next', [$this, 'groupChatNextTransition']);
	}

	private function initPrivateFSM(FiniteStateMachine $fsm)
	{
		$fsm->getDispatcher()->addListener('finite.post_transition.live', [$this, 'groupChatLiveTransition']);
		$fsm->getDispatcher()->addListener('finite.post_transition.mute', [$this, 'groupChatMuteTransition']);
		$fsm->getDispatcher()->addListener('finite.post_transition.info', [$this, 'groupChatInfoTransition']);
		$fsm->getDispatcher()->addListener('finite.post_transition.curr', [$this, 'groupChatCurrTransition']);
		$fsm->getDispatcher()->addListener('finite.post_transition.next', [$this, 'groupChatNextTransition']);
	}

	function groupChatLiveTransition(\Finite\Event\TransitionEvent $e){
		$params = $e->getProperties();
		$chat   = array_get($params, 'chat');
		if (is_null($chat)){
			throw new \Exception("Chat is null");
		}
		$this->liveCommand($chat);
	}


	function groupChatMuteTransition(\Finite\Event\TransitionEvent $e){
		$params = $e->getProperties();
		$chat   = array_get($params, 'chat');
		if (is_null($chat)){
			throw new \Exception("Chat is null");
		}
		$this->muteCommand($chat);
	}

	function groupChatInfoTransition(\Finite\Event\TransitionEvent $e){
		$params = $e->getProperties();
		$chat   = array_get($params, 'chat');
		if (is_null($chat)){
			throw new \Exception("Chat is null");
		}
		$this->infoCommand($chat);
	}

	function groupChatCurrTransition(\Finite\Event\TransitionEvent $e){
		$params = $e->getProperties();
		$chat   = array_get($params, 'chat');
		if (is_null($chat)){
			throw new \Exception("Chat is null");
		}
		$this->currCommand($chat);
	}

	function groupChatNextTransition(\Finite\Event\TransitionEvent $e){
		$params = $e->getProperties();
		$chat   = array_get($params, 'chat');
		$args   = array_get($params, 'args');
		if (is_null($chat)){
			throw new \Exception("Chat is null");
		}
		$this->nextCommand($chat, $args);
	}

	function privateChatLiveTransition(\Finite\Event\TransitionEvent $e){
		$this->log->warn("Executed from privateChatLiveTransition");
	}

	function privateMuteTransition(\Finite\Event\TransitionEvent $e){
		$this->log->warn("Executed from privateMuteTransition");
	}

	private function liveCommand($chat)
	{
        $this->sendMessage($this->lang->trans('live.turnedOn'), $chat);
	}

	private function muteCommand($chat)
	{
        $this->sendMessage($this->lang->trans('live.turnedOff'), $chat);

	}

	private function infoCommand($chat)
	/** @var PrivateChat|GroupChat $chat */
	{
        $message = $this->lang->trans('command.info', ['%status%' => $this->chatIdToFsm($chat->getChatId())->getCurrentState()]);
		$this->sendMessage($message, $chat);
	}

	private function chatIdToFsm($chatId){
		if (array_has($this->states['private'], $chatId)){
			return array_get($this->states['private'], $chatId);
		}
		if (array_has($this->states['group'], $chatId)){
			return array_get($this->states['group'], $chatId);
		}
		throw new \Exception("FiniteStateMachine not found");
	}

	private function currCommand($chat)
	{
		$message = "";
		$currentMatchesCount = MatchQuery::create()->where('matches.status = ?', 'IN_PLAY')->count();
		if ($currentMatchesCount > 0){

            $message .= $this->lang->transChoice('command.curr.currentMatches', $currentMatchesCount);

			$currentMatches = MatchQuery::create()->where('matches.status = ?', 'IN_PLAY')->find();

			foreach ($currentMatches as $currentMatch){
                $message .= $this->lang->trans(
                    'command.curr.currentMatch',
                    [
                        '%homeTeamName%'  => $currentMatch->getHomeTeam()->getName(),
                        '%homeTeamEmoji%' => $currentMatch->getHomeTeam()->getEmoji(),
                        '%awayTeamEmoji%' => $currentMatch->getAwayTeam()->getEmoji(),
                        '%awayTeamName%'  => $currentMatch->getAwayTeam()->getName(),
                        '%homeTeamGoals%' => $currentMatch->getHomeTeamGoals(),
                        '%awayTeamGoals%' => $currentMatch->getAwayTeamGoals()
                    ]
                );
			}
		} else {
			$message .= $this->lang->trans('command.curr.noCurrentMatches');
		}
		$this->sendMessage($message, $chat);

	}

	private function nextCommand($chat, $args)
	{
		$m = ($args && is_numeric($args[0])) ? $args[0] : 1;
		$i = 0;
		$message = $this->lang->transChoice('command.next.nextMatches', $m) . "\n";
		$nextMatches = MatchQuery::create()->where('matches.status = ?', 'TIMED')->orderByDate()->find();
		foreach ($nextMatches as $nextMatch){
			$difference = Helper::timeDifference($nextMatch->getDate());
			$message .= $this->lang->trans(
				'command.next.nextMatch',
				[
					'%homeTeamName%'  => $nextMatch->getHomeTeam()->getName(),
					'%homeTeamEmoji%' => $nextMatch->getHomeTeam()->getEmoji(),
					'%awayTeamEmoji%' => $nextMatch->getAwayTeam()->getEmoji(),
					'%awayTeamName%'  => $nextMatch->getAwayTeam()->getName(),
					'%difference%'    => $difference
				]
			);
			$i++;
			if ($i < $m)
				$message .= "\n";
			else
				break;
		}
		$this->sendMessage($message, $chat);
	}

	private function initTranslation()
	{
		$this->lang = new Translator('de_DE', new MessageSelector());
		$this->lang->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());
		$this->lang->addResource('php', __DIR__ . '/../../../res/lang/de_DE.php', 'de_DE');
	}

	private function initConfig()
	{
		$this->config = include(__DIR__ . '/../../../res/conf/config.php');
		$this->api_config = include(__DIR__ . '/../../../res/conf/api_config.php');

	}
}