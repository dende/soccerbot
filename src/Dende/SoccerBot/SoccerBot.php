<?php

namespace Dende\SoccerBot;

use Dende\SoccerBot\Exception;
use Dende\SoccerBot\Model\ChatFactory;
use Analog\Analog;
use Dende\SoccerBot\Model\ChatInterface;
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
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Message;

/**
 * Class SoccerBot
 */
class SoccerBot
{
	protected $config;
	protected $api_config;
	protected $rootData;
	/** @var  \GuzzleHttp\Client */
	protected $client;
	protected $header;
	protected $offset;
	/** @var \Telegram\Bot\Api */
	protected $telegram;
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
	}

	function run(){
		while (true){
			$this->updateMatches();
			$updates = $this->telegram->getUpdates([
                'offset' => $this->offset,
                'limit' => TELEGRAM_BOT_LIMIT,
                'timeout' => TELEGRAM_BOT_TIMEOUT
            ]);
			foreach ($updates as $update){
                try {
                    $this->handle($update);
                } catch (Exception\EmptyMessageException $e){
                    //not too bad
                } catch (Exception\CommandNotFoundException $e){
                    //not too bad
                } catch (Exception\InvalidCommandStringException $e){
                    //not too bad
                }
            }
		}
	}

	function handle(TelegramUpdate $update){
		$this->offset = $update->getUpdateId() + 1;
		
		$message = $update->getMessage();
		
		if(!$message){
			throw new Exception\EmptyMessageException();
		}
		
		$chat = ChatFactory::create($message->getChat());

        $message = $chat->handle($update);

        $this->sendMessage($message, $chat);

	}


	function updateMatches(){
        Analog::log("Updating matches");
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
        Analog::log($message);
		throw new \Exception($message);
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
            Analog::log('Updated the Teams in the database');
		} else {
            Analog::log('No updates for the teams needed');
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
            Analog::log('Updated the Matches in the database');
		} else {
            Analog::log('No updates for the matches needed');
		}
	}

	private function initLog()
	{
        Analog::handler(\Analog\Handler\Multi::init(array(
            Analog::WARNING => \Analog\Handler\File::init (array_get($this->config, 'log')),
            Analog::DEBUG   => \Analog\Handler\Stderr::init ()
        )));
    }

	private function initTelegramApi()
	{
		$this->offset = 0;
		$this->telegram = new TelegramApi(TELEGRAM_BOT_TOKEN);
	}

	private function initFooballDataApi()
	{
		$this->client = new \GuzzleHttp\Client();
		$this->header = array('headers' => array('X-Auth-Token' => FOOTBALL_DATA_API_TOKEN));
		$response = $this->client->get(FOOTBALL_DATA_ROOT_URL, $this->header);
		$this->rootData = json_decode($response->getBody()->getContents(), true);
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
				$message = new Message();

				$homeTeam = $match->getHomeTeam();
				$awayTeam = $match->getAwayTeam();

				/** @var GroupChat $chat */
				$chat = $fsm->getObject();

				if (array_get($newData, 'status') == 'IN_PLAY'){
					$message->addLine(
                        'live.matchStarted',
                        [
                            '%homeTeamName%'  => $homeTeam->getName(),
                            '%awayTeamName%'  => $awayTeam->getName(),
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
                    $message->addLine(
                        'live.teamScored',
                        [
                            '%teamScoredName%'   => $teamScoredName,
                            '%teamConcededName%' => $teamConcededName,
                            '%goals%'            => $goalsScored
                        ],
                        $goalsScored
                    );
                    $message->addLine(
                        'live.newScore',
                        [
                            '%homeTeamGoals%' => $match->getHomeTeamGoals(),
                            '%awayTeamGoals%' => $match->getAwayTeamGoals(),
                        ]
                    );
                }

				if (array_get($newData, 'status') == 'FINISHED'){
					$message->addLine(
                        'live.finished',
                        [
                            '%homeTeamName%' => $homeTeam->getName(),
                            '%awayTeamName%' => $awayTeam->getName()
                        ]
                    );
                    $message->addLine(
                        'live.finalScore',
                        [
                            '%homeTeamGoals%' => $match->getHomeTeamGoals(),
                            '%awayTeamGoals%' => $match->getAwayTeamGoals(),
                        ]
                    );
				}
				$this->sendMessage($message, $chat);
			}
		}
	}

	private function sendMessage(Message $message, ChatInterface $chat){
		/** @var PrivateChat|GroupChat $chat */
		$this->telegram->sendMessage(['chat_id' => $chat->getChatId(), 'text' => $message->translate($this->lang), 'parse_mode' => 'Markdown']);
	}

	private function liveCommand($chat)
	{
        $this->sendMessage($this->lang->trans('live.turnedOn'), $chat);
	}

	private function muteCommand($chat)
	{
        $this->sendMessage($this->lang->trans('live.turnedOff'), $chat);

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


	private function initTranslation()
	{
		$this->lang = new Translator('de_DE', new MessageSelector());
		$this->lang->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());
		$this->lang->addResource('php', __DIR__ . '/../../../res/lang/de_DE.php', 'de_DE');
	}

	private function initConfig()
	{
		$this->config = include(__DIR__ . '/../../../res/conf/config.php');
	}
}