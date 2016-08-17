<?php


namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\PrivateChat;
use Finite\StatefulInterface;
use Telegram\Bot\Objects\Message as TelegramMessage;

class InfoCommand extends AbstractCommand
{

    protected function runPrivate(PrivateChat $chat, TelegramMessage $message){
        return $this->runBoth($chat);
    }

    protected function runGroup(GroupChat $chat, TelegramMessage $message){
        return $this->runBoth($chat);
    }

    protected function runBoth($chat){
        /** @var GroupChat|PrivateChat $chat */

        $response = $chat->getLiveticker()?'command.info.liveticker':'command.info.muted';
        return new Message($response);
    }
}