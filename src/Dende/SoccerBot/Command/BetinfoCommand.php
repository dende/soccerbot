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

class BetinfoCommand extends AbstractCommand
{
    function runPrivate(Chat $chat, TelegramMessage $message){

        /*
        $openMatches = $this->matchRepo->getOpenMatchesForChat($chat);
        $bets = BetQuery::create()->filterByPrivateChat($chat)->find();

        if ($openMatches->isEmpty() && $bets->isEmpty()){
            return new Message('command.betinfo.nothing');
        }

        $telegram = $this->chatRepo->getTelegramApi();

        if ($bets->isEmpty()){
            $response = new Message('command.betinfo.noBets');
        } else {
            $response = new Message('command.betinfo.followingBets');
            foreach ($bets as $bet){
                $response->addLine('command.betinfo.bet', [
                    '%homeTeamName%' => $bet->getMatch()->getHomeTeam()->getName(),
                    '%awayTeamName%' => $bet->getMatch()->getAwayTeam()->getName(),
                    '%bet%' => $bet->getBetString(),
                ]);
            }
        }

        $telegram->sendMessage($chat, $response);

        if ($openMatches->isEmpty()){
            $response = new Message('command.betinfo.noOpen');
        } else {
            $response = new Message('command.betinfo.followingOpen');
            /** @var Match $match
            foreach ($openMatches as $match){
                $response->addLine('command.betinfo.open',[
                    '%homeTeamName%' => $match->getHomeTeam()->getName(),
                    '%awayTeamName%' => $match->getAwayTeam()->getName(),
                    '%date%' => $match->getDate('d.m.'),
                ]);
            }
        }
        return $response;

        */
        return new Response();
    }

    function runGroup(Chat $chat, TelegramMessage $message){
        return new Message('command.bet.group');
    }

}