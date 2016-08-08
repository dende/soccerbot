<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 24.06.2016
 * Time: 17:18
 */

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\ChatInterface;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\PrivateChat;
use Finite\State\StateInterface;

abstract class AbstractCommand
{
    public function run(ChatInterface $chat, $args, StateInterface $state){
        if ($chat instanceof PrivateChat){
            return $this->runPrivate($chat, $args, $state);
        } else if ($chat instanceof GroupChat){
            return $this->runGroup($chat, $args, $state);
        }
    }

    abstract protected function runPrivate(PrivateChat $chat, $args, StateInterface $state);

    abstract protected function runGroup(GroupChat $chat, $args, StateInterface $state);
}