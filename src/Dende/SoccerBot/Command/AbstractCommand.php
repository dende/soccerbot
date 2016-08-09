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
use Dende\SoccerBot\Repository\ChatRepository;
use Finite\State\StateInterface;

abstract class AbstractCommand
{
    protected $args;
    protected $chatRepo;

    public function __construct(ChatRepository $chatRepo)
    {
        $this->chatRepo = $chatRepo;
    }

    public function run(ChatInterface $chat){
        if ($chat instanceof PrivateChat){
            return $this->runPrivate($chat);
        } else if ($chat instanceof GroupChat){
            return $this->runGroup($chat);
        }
    }

    abstract protected function runPrivate(PrivateChat $chat);

    abstract protected function runGroup(GroupChat $chat);

    public function setArgs($args){
        $this->args = $args;
    }

    public function getArgs(){
        return $this->args;
    }
}