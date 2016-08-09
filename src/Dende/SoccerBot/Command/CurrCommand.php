<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 24.06.2016
 * Time: 17:19
 */

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\ChatInterface;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\MatchQuery;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Finite\State\StateInterface;

class CurrCommand extends AbstractCommand
{

    protected function runPrivate(PrivateChat $chat){
        return $this->runBoth($chat);
    }

    protected function runGroup(GroupChat $chat){
        return $this->runBoth($chat);
    }

    protected function runBoth(ChatInterface $chat){
        $currentMatchesCount = MatchQuery::create()->where('matches.status = ?', 'IN_PLAY')->count();
        if ($currentMatchesCount > 0){

            $message = new Message('command.curr.currentMatches', $currentMatchesCount, true);

            $currentMatches = MatchQuery::create()->where('matches.status = ?', 'IN_PLAY')->find();

            foreach ($currentMatches as $currentMatch){
                $message->addLine(
                    'command.curr.currentMatch',
                    [
                        '%homeTeamName%'  => $currentMatch->getHomeTeam()->getName(),
                        '%awayTeamName%'  => $currentMatch->getAwayTeam()->getName(),
                        '%homeTeamGoals%' => $currentMatch->getHomeTeamGoals(),
                        '%awayTeamGoals%' => $currentMatch->getAwayTeamGoals()
                    ]
                );
            }
        } else {
            $message = new Message('command.curr.noCurrentMatches');
        }

        return $message;
    }
}