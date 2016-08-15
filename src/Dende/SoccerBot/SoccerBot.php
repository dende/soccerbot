<?php

namespace Dende\SoccerBot;

use Analog\Analog;
use Dende\SoccerBot\Command\CommandFactory;
use Dende\SoccerBot\Model\ChatFactory;
use Dende\SoccerBot\Model\FootballApi;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Model\Message;
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
        Analog::handler(\Analog\Handler\Stderr::init());
        $this->footballApi = new FootballApi();
        $this->telegramApi = new TelegramApi($this->lang);
        $this->chatRepo = new ChatRepository($this->telegramApi, $this->lang);
        $this->teamRepo = new TeamRepository($this->footballApi);
        $this->matchRepo = new MatchRepository($this->footballApi);
        $this->commandFactory = new CommandFactory($this->chatRepo);
	}

	function run(){
		while (true){

			$liveInfos = $this->matchRepo->update();
            $livetickerChats = $this->chatRepo->getLivetickerChats();

            if (!empty($liveInfos) && !empty($livetickerChats)){
                foreach ($livetickerChats as $chat){
                    foreach ($liveInfos as $info ){
                        $this->telegramApi->sendMessage($info, $chat);
                    }
                }
            }

			$updates = $this->telegramApi->getUpdates();

            foreach ($updates as $update){
                try {
                    $message = $update->getMessage();

                    if(!$message){
                        throw new \Dende\SoccerBot\Exception\EmptyMessageException();
                    }

                    $chat = ChatFactory::create($message->getChat());

                    list($commandString, $args) = $this->commandFactory->commandStringFromMessage($message);

                    if (is_null($commandString)){
                        $response = $chat->handle($message);
                    } else {
                        $command = $this->commandFactory->createFromString($commandString, $args);
                        $response = $command->run($chat, $message);
                    }


                    $this->telegramApi->sendMessage($response, $chat);
                    $this->telegramApi->setOffset($update->getUpdateId() + 1);

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