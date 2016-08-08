<?php


namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\PrivateChat;
use Finite\State\StateInterface;

class InfoCommand extends AbstractCommand
{

    protected function runPrivate(PrivateChat $chat, $args, StateInterface $state){
        return new Message('command.info', ['%status%' => $state->getName()]);
    }

    protected function runGroup(GroupChat $chat, $args, StateInterface $state){

    }
}