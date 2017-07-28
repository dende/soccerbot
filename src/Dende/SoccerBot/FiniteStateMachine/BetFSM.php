<?php

namespace Dende\SoccerBot\FiniteStateMachine;
use Dende\SoccerBot\Model\Chat;
use Finite\Loader\ArrayLoader;
use Finite\StateMachine\StateMachine as FiniteStateMachine;


class BetFSM
{
    const STATUS_INACTIVE = 'inactive';
    const STATUS_GOALS_ASKED = 'waiting_for_bet';
    const TRANSITION_ASK_GOALS = 'ask_goals';
    const TRANSITION_NEXT = 'next';
    const TRANSITION_LATER = 'later';
    const TRANSITION_DONE = 'done';

    public static function create(Chat $chat){

        $betFsm = new FiniteStateMachine($chat);
        $betLoader = new ArrayLoader([
            'class' => '\Dende\SoccerBot\Model\Chat',
            'property_path' => 'betstatus',
            'states' => [
                BetFSM::STATUS_INACTIVE => ['type' => 'initial'],
                BetFSM::STATUS_GOALS_ASKED => ['type' => 'normal'],
            ],
            'transitions' => [
                BetFSM::TRANSITION_ASK_GOALS => [
                'from' => BetFSM::STATUS_INACTIVE,
                'to' => BetFSM::STATUS_GOALS_ASKED,
            ],
                BetFSM::TRANSITION_DONE => [
                'from' => BetFSM::STATUS_GOALS_ASKED,
                'to' => BetFSM::STATUS_INACTIVE,
            ],
                BetFSM::TRANSITION_LATER => [
                'from' => BetFSM::STATUS_GOALS_ASKED,
                'to' => BetFSM::STATUS_INACTIVE
            ],
                BetFSM::TRANSITION_NEXT => [
                'from' => BetFSM::STATUS_GOALS_ASKED,
                'to' => BetFSM::STATUS_GOALS_ASKED,
            ]
            ]
        ]);
        $betLoader->load($betFsm);
        $betFsm->initialize();
        return $betFsm;

    }
}