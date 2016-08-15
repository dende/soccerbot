<?php


namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Telegram\Bot\Objects\Message as TelegramMessage;

class NoopCommand extends AbstractCommand

{
    protected function runPrivate(PrivateChat $chat, TelegramMessage $message){
        return $this->runBoth();
    }

    protected function runGroup(GroupChat $chat, TelegramMessage $message){
        return $this->runBoth();
    }

    private function runBoth(){
        switch ($this->args){
            case null:
            case "start":
                return null;
                break;
            default:
                break;
        }
        return new Message('command.noop', ['%command%' => $this->args]);
    }

}