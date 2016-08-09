<?php

namespace Dende\SoccerBot;

use Analog\Analog;
use Dende\SoccerBot\Command\CommandFactory;
use Dende\SoccerBot\Model\FootballApi;
use Dende\SoccerBot\Model\TelegramApi;
use Dende\SoccerBot\Repository\ChatRepository;
use Dende\SoccerBot\Repository\MatchRepository;
use Dende\SoccerBot\Repository\TeamRepository;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;

/**
 * Class SoccerBot
 */
class SoccerBot
{
	protected $config;
	/** @var \Dende\SoccerBot\Model\TelegramApi */
	protected $telegramApi;
    /** @var  \Symfony\Component\Translation\Translator */
	protected $lang;
    /** @var  MatchRepository */
    protected $matchRepo;
    /** @var  TeamRepository */
    protected $teamRepo;
    /** @var ChatRepository */
    protected $chatRepo;
    /** @var FootballApi */
    protected $footballApi;
    /** @var  CommandFactory */
    protected $commandFactory;


    function init(){
        $this->config = include(__DIR__ . '/../../../res/conf/config.php');
        $this->lang = new Translator('de_DE', new MessageSelector());
        $this->lang->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());
        $this->lang->addResource('php', __DIR__ . '/../../../res/lang/de_DE.php', 'de_DE');
        Analog::handler(\Analog\Handler\Multi::init([
            Analog::WARNING => \Analog\Handler\File::init (array_get($this->config, 'log')),
            Analog::DEBUG   => \Analog\Handler\Stderr::init ()
        ]));
        $this->footballApi = new FootballApi();
        $this->chatRepo = new ChatRepository();
        $this->matchRepo = new MatchRepository($this->footballApi);
        $this->teamRepo = new TeamRepository($this->footballApi);
        $this->commandFactory = new CommandFactory($this->chatRepo);
        $this->telegramApi = new TelegramApi($this->lang, $this->commandFactory);
        $this->teamRepo->init();
        $this->matchRepo->init();
	}

	function run(){
		while (true){

			$liveInfos = $this->matchRepo->update();

            foreach ($liveInfos as $data) {
                $this->telegramApi->liveticker($data['match'], $data['info']);
            }

			$this->telegramApi->update();
		}
	}

}