<?php

namespace Dende\SoccerBot\Model\FiniteStateMachine;
use Dende\SoccerBot\Model\Chat;
use Finite\Loader\ArrayLoader;
use Finite\StateMachine\StateMachine as FiniteStateMachine;


class BetFSM
{
    const STATUS_INACTIVE = 'inactive';
    const STATUS_WAITING_FOR_BET = 'waiting_for_bet';
    const TRANSITION_ASK_GOALS = 'ask_goals';
    const TRANSITION_NEXT = 'next';
    const TRANSITION_LATER = 'later';
    const TRANSITION_DONE = 'done';

    const REGEX_BET = '/^[0-9]{1,2}:[0-9]{1,2}$/';


    public static function create(Chat $chat){

        $betFsm = new FiniteStateMachine($chat);
        $betLoader = new ArrayLoader([
            'class' => 'PrivateChat',
            'property_path' => 'betstatus',
            'states' => [
                BetFSM::STATUS_INACTIVE => ['type' => 'initial'],
                BetFSM::STATUS_WAITING_FOR_BET => ['type' => 'normal'],
            ],
            'transitions' => [
                BetFSM::TRANSITION_ASK_GOALS => [
                'from' => BetFSM::STATUS_INACTIVE,
                'to' => BetFSM::STATUS_WAITING_FOR_BET,
            ],
                BetFSM::TRANSITION_DONE => [
                'from' => BetFSM::STATUS_WAITING_FOR_BET,
                'to' => BetFSM::STATUS_INACTIVE,
            ],
                BetFSM::TRANSITION_LATER => [
                'from' => BetFSM::STATUS_WAITING_FOR_BET,
                'to' => BetFSM::STATUS_INACTIVE
            ],
                BetFSM::TRANSITION_NEXT => [
                'from' => BetFSM::STATUS_WAITING_FOR_BET,
                'to' => BetFSM::STATUS_WAITING_FOR_BET,
            ]
            ]
        ]);
        $betLoader->load($betFsm);
        $betFsm->initialize();
        return $betFsm;

    }
}