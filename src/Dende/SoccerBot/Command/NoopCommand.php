<?php


namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Chat;
use Dende\SoccerBot\Telegram\Response;
use Telegram\Bot\Objects\Message as TelegramMessage;

class NoopCommand extends AbstractCommand
{

    function run(Chat $chat, TelegramMessage $message){
        switch ($this->args){
            case null:
            case "start":
                return null;
                break;
            default:
                break;
        }
        return new Response('command.noop', ['%command%' => $this->args]);
    }

    function runGroup(Chat $chat, TelegramMessage $message)
    {
        // TODO: Implement runGroup() method.
    }

    function runPrivate(Chat $chat, TelegramMessage $message)
    {
        // TODO: Implement runPrivate() method.
    }
}