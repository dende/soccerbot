<?php

namespace Dende\SoccerBot;

use Analog\Analog;
use Dende\SoccerBot\Command\ResponseFactory;
use Dende\SoccerBot\Command\CommandFactory;
use Dende\SoccerBot\FootballData\Api as FootballDataApi;
use Dende\SoccerBot\Telegram\Api as TelegramApi;
use Dende\SoccerBot\Repository\ChatRepository;
use Dende\SoccerBot\Repository\MatchRepository;
use Dende\SoccerBot\Repository\TeamRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;

/**
 * Class SoccerBot
 */
class SoccerBot
{
	protected $config;
	/** @var \Dende\SoccerBot\Telegram\Api */
	protected $telegramApi;
    /** @var  \Symfony\Component\Translation\Translator */
	protected $lang;
    /** @var  MatchRepository */
    protected $matchRepo;
    /** @var  TeamRepository */
    protected $teamRepo;
    /** @var ChatRepository */
    protected $chatRepo;
    /** @var FootballDataApi */
    protected $footballDataApi;
    /** @var  CommandFactory */
    protected $commandFactory;
    /** @var ResponseFactory */
    protected $responseFactory;


    function init(){
        $this->config = include(__DIR__ . '/../../../res/conf/config.php');
        $this->lang = new Translator('de_DE', new MessageSelector());
        $this->lang->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());
        $this->lang->addResource('php', __DIR__ . '/../../../res/lang/de_DE.php', 'de_DE');
        Analog::handler(\Analog\Handler\Stderr::init());
        $this->telegramApi = new TelegramApi($this->lang);
        $this->footballDataApi = new FootballDataApi();
        $this->teamRepo = new TeamRepository($this->footballDataApi);
        $this->matchRepo = new MatchRepository($this->footballDataApi);
        $this->commandFactory = new CommandFactory($this->matchRepo, $this->teamRepo);
        $this->chatRepo = new ChatRepository($this->lang);
        $this->responseFactory = new ResponseFactory($this->commandFactory);
	}

	function run(){
		while (true){

		    //get new liveticker information
			$liveInfos = $this->matchRepo->update();
			//get liveticker subscribers
			$liveTickerChats = $this->chatRepo->getLivetickerChats();

			//message them the news
			foreach ($liveTickerChats as $liveTickerChat){
			    foreach ($liveInfos as $liveInfo){
                    $this->telegramApi->sendMessage($liveTickerChat, $liveInfo);
                }
            }

            //receive new telegram update
			try {
			    $updates = $this->telegramApi->getUpdates();
            } catch (Exception $e){
                Analog::log('Telegram getupdate threw an exception', Analog::ERROR);
                continue;
            }

            foreach ($updates as $update){
                try {
                    $chat     = $this->chatRepo->createFromUpdate($update);
                    $command  = $this->commandFactory->createFromUpdate($update);
                    $response = $this->responseFactory->createResponse($chat, $command);
                    $this->telegramApi->respond($chat, $response);

                } catch (\Dende\SoccerBot\Exception\EmptyMessageException $e){
                    //not too bad
                } catch (\Dende\SoccerBot\Exception\CommandNotFoundException $e){
                    //not too bad
                } catch (\Dende\SoccerBot\Exception\InvalidCommandStringException $e){
                    //not too bad
                }
            }

        }
	}

}