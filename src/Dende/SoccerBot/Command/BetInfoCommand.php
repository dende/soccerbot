<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 24.06.2016
 * Time: 17:19
 */

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Bet;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Telegram\Bot\Objects\Message as TelegramMessage;

class BetInfoCommand extends AbstractCommand
{
    protected function runPrivate(PrivateChat $chat, TelegramMessage $message){
    }

    protected function runGroup(GroupChat $chat, TelegramMessage $message){
        return new Message('command.bet.group');
    }
    
}