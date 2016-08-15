<?php


namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\ChatInterface;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Telegram\Bot\Objects\Message as TelegramMessage;

class LiveCommand extends AbstractCommand
{

    protected function runPrivate(PrivateChat $chat, TelegramMessage $message)
    {
        return $this->runBoth($chat);
    }

    protected function runGroup(GroupChat $chat, TelegramMessage $message)
    {
        return $this->runBoth($chat);
    }

    private function runBoth(ChatInterface $chat){
        /** @var $chat GroupChat|PrivateChat */
        if ($chat->getLiveticker()){
            $response = new Message('command.live.alreadyOn');
        } else {
            $this->chatRepo->live($chat);
            $response = new Message('command.live.turnedOn');
        }
        return $response;
    }
}