<?php

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Base\BetQuery;
use Dende\SoccerBot\Model\Chat;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Dende\SoccerBot\Telegram\Response;
use Illuminate\Database\Eloquent\Collection;
use Telegram\Bot\Objects\Message as TelegramMessage;

class BetinfoCommand extends AbstractCommand
{
    function runPrivate(Chat $chat, TelegramMessage $message){


        $openMatches = $this->matchRepo->getOpenMatchesForChat($chat);
        /** @var Collection $placedBets */
        $placedBets = $chat->bets()->get();

        if ($openMatches->isEmpty() && $placedBets->isEmpty()){
            return new Response('command.betinfo.nothing');
        }

        if ($placedBets->isEmpty()){
            $response = new Response('command.betinfo.noBets');
        } else {
            $response = new Response('command.betinfo.followingBets');
            foreach ($placedBets as $bet){
                $response->addLine($chat->getLang()->trans('command.betinfo.bet', [
                    '%homeTeamName%' => $bet->match->homeTeam->name,
                    '%awayTeamName%' => $bet->match->awayTeam->name,
                    '%bet%' => $bet->getBetString(),
                ]));
            }
        }


        if ($openMatches->isEmpty()){
            $response->addLine($chat->getLang()->trans('command.betinfo.noOpen'));
        } else {
            $response->addLine($chat->getLang()->trans('command.betinfo.followingOpen'));
            /** @var Match $match */
            foreach ($openMatches as $match){
                $response->addLine($chat->getLang()->trans('command.betinfo.open',[
                    '%homeTeamName%' => $match->homeTeam->name,
                    '%awayTeamName%' => $match->awayTeam->name,
                    '%date%' => $match->date->format('d.m.Y'),
                ]));
            }
        }
        return $response;
    }

    function runGroup(Chat $chat, TelegramMessage $message){
        return new Response('command.bet.group');
    }

}

