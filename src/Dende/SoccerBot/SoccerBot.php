<?php

namespace Dende\SoccerBot;

use Analog\Analog;
use Dende\SoccerBot\Model\FootballApi;
use Dende\SoccerBot\Repository\MatchRepository;
use Dende\SoccerBot\Repository\TeamRepository;
use Finite\StateMachine\StateMachine as FiniteStateMachine;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Message;

/**
 * Class SoccerBot
 */
class SoccerBot
{
	protected $config;
	protected $api_config;
	/** @var  \GuzzleHttp\Client */
	protected $client;
	protected $header;
	protected $offset;
	/** @var \Dende\SoccerBot\Model\TelegramApi */
	protected $telegramApi;
    /** @var  \Symfony\Component\Translation\Translator */
	protected $lang;
	protected $states;
    /** @var  MatchRepository */
    protected $matchRepo;
    /** @var  TeamRepository */
    protected $teamRepo;
    /** @var FootballApi */
    protected $footballApi;

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

			$liveInfos = $this->matchRepo->update();

            foreach ($liveInfos as $data) {
                $this->liveticker($data['match'], $data['info']);
            }

			$this->telegramApi->update();
		}
	}

	private function initDb()
	{

	    $this->teamRepo->init();

        $this->matchRepo->init();

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
	    $this->telegramApi = new \Dende\SoccerBot\Model\TelegramApi($this->lang);
	}

	private function initFooballDataApi()
	{
        $this->footballApi = new FootballApi();
        $this->matchRepo = new MatchRepository($this->footballApi);
        $this->teamRepo = new TeamRepository($this->footballApi);
    }

	private function liveticker(Match $match, $info) {

        $fsms = array_merge($this->states['private'], $this->states['group']);
        foreach ($fsms as $fsm){
            /** @var FiniteStateMachine $fsm */
            if ($fsm->getCurrentState() == 'liveticker'){
                $message = new Message();

                $homeTeam = $match->getHomeTeam();
                $awayTeam = $match->getAwayTeam();

                /** @var GroupChat $chat */
                $chat = $fsm->getObject();

                if (array_get($info, 'status') == 'IN_PLAY'){
                    $message->addLine(
                        'live.matchStarted',
                        [
                            '%homeTeamName%'  => $homeTeam->getName(),
                            '%awayTeamName%'  => $awayTeam->getName(),
                        ]
                    );
                }

                if (array_has($info, 'homeTeamGoalsScored')){
                    $goalsScored         = array_get($info, 'homeTeamGoalsScored');
                    $teamScoredName      = $homeTeam->getName();
                    $teamConcededName    = $awayTeam->getName();
                } else if (array_has($info, 'awayTeamGoalsScored')){
                    $goalsScored         = array_get($info, 'awayTeamGoalsScored');
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

                if (array_get($info, 'status') == 'FINISHED'){
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
                $this->telegramApi->sendMessage($message, $chat);
            }
        }
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