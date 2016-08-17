<?php

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Base\BetQuery;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Telegram\Bot\Objects\Message as TelegramMessage;

class CancelCommand extends AbstractCommand
{
    protected function runPrivate(PrivateChat $chat, TelegramMessage $message){
        if ($chat->getBetstatus() === PrivateChat::BET_STATUS_GOALS_ASKED){
            $fsm = $chat->getBetFsm();
            $fsm->apply(PrivateChat::BET_TRANSITION_DONE);
            $chat->save();
        }
    }

    protected function runGroup(GroupChat $chat, TelegramMessage $message){
        return new Message('command.bet.group');
    }

}