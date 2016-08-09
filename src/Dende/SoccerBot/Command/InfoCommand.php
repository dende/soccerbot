<?php


namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\ChatInterface;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\PrivateChat;
use Finite\State\StateInterface;
use Finite\StatefulInterface;

class InfoCommand extends AbstractCommand
{

    protected function runPrivate(PrivateChat $chat){
        $this->runBoth($chat);
    }

    protected function runGroup(GroupChat $chat){
        $this->runBoth($chat);
    }

    protected function runBoth(StatefulInterface $chat){
        return new Message('command.info', ['%status%' => $chat->getFiniteState()]);
    }
}