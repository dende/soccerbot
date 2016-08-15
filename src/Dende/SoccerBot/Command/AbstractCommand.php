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
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Dende\SoccerBot\Repository\ChatRepository;
use Telegram\Bot\Objects\Message as TelegramMessage;

abstract class AbstractCommand
{
    protected $args;
    protected $chatRepo;

    public function __construct(ChatRepository $chatRepo)
    {
        $this->chatRepo = $chatRepo;
    }

    /**
     * @param ChatInterface $chat
     * @param TelegramMessage $message
     * @return Message
     */
    public function run(ChatInterface $chat, TelegramMessage $message){
        if ($chat instanceof PrivateChat){
            return $this->runPrivate($chat, $message);
        } else if ($chat instanceof GroupChat){
            return $this->runGroup($chat, $message);
        }
    }

    abstract protected function runPrivate(PrivateChat $chat, TelegramMessage $message);

    abstract protected function runGroup(GroupChat $chat, TelegramMessage $message);

    public function setArgs($args){
        $this->args = $args;
    }

    public function getArgs(){
        return $this->args;
    }
}