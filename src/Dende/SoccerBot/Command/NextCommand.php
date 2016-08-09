<?php

namespace Dende\SoccerBot\Command;

use Dende\SoccerBot\Helper;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\MatchQuery;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;

class NextCommand extends AbstractCommand
{

    protected function runPrivate(PrivateChat $chat){
        return $this->runBoth();
    }

    protected function runGroup(GroupChat $chat){
        return $this->runBoth();
    }

    protected function runBoth(){
        $m = ($this->args && is_numeric($this->args[0])) ? $this->args[0] : 1;
        $message = new Message('command.next.nextMatches', [], $m);
        $nextMatches = MatchQuery::create()->where('matches.status = ?', 'TIMED')->orderByDate()->limit($m)->find();
        foreach ($nextMatches as $nextMatch) {
            $difference = Helper::timeDifference($nextMatch->getDate());
            $message->addLine(
                'command.next.nextMatch',
                [
                    '%homeTeamName%' => $nextMatch->getHomeTeam()->getName(),
                    '%awayTeamName%' => $nextMatch->getAwayTeam()->getName(),
                    '%difference%' => $difference
                ]
            );
        }
        return $message;
    }
}