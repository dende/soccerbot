<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 24.06.2016
 * Time: 17:19
 */

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Helper;
use Dende\SoccerBot\Model\ChatInterface;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\MatchQuery;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Finite\State\StateInterface;

class NextCommand extends AbstractCommand
{

    protected function runPrivate(PrivateChat $chat, $args, StateInterface $state){
        return $this->runBoth($chat, $args, $state);
    }

    protected function runGroup(GroupChat $chat, $args, StateInterface $state){
        return $this->runBoth($chat, $args, $state);
    }

    protected function runBoth(ChatInterface $chat, $args, StateInterface $state){
        $m = ($args && is_numeric($args[0])) ? $args[0] : 1;
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