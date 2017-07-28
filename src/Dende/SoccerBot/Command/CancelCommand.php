<?php

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Base\BetQuery;
use Dende\SoccerBot\Model\Chat;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Dende\SoccerBot\Telegram\Response;
use Telegram\Bot\Objects\Message as TelegramMessage;

class CancelCommand extends AbstractCommand
{
     function runPrivate(Chat $chat, TelegramMessage $message){
         return new Response();
    }

     function runGroup(Chat $chat, TelegramMessage $message){
         return new Response();
     }

}