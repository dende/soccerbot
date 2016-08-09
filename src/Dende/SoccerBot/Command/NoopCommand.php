<?php


namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;

class NoopCommand extends AbstractCommand
{
    protected function runPrivate(PrivateChat $chat){
        return $this->runBoth();
    }

    protected function runGroup(GroupChat $chat){
        return $this->runBoth();
    }

    private function runBoth(){
        return new Message('command.noop', ['%command%' => $this->args]);
    }

}