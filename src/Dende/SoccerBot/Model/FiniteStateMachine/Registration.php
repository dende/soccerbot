<?php

namespace Dende\SoccerBot\Model\FiniteStateMachine;
use Dende\SoccerBot\Model\PrivateChat;
use Finite\Loader\ArrayLoader;
use Finite\StateMachine\StateMachine as FiniteStateMachine;


class Registration
{

    const STATUS_UNREGISTERED = 'unregistered';
    const STATUS_KEEP_TELEGRAM_USERNAME_ASKED = 'keep_telegram_username_asked';
    const STATUS_ENTER_USERNAME_ASKED = 'enter_username_asked';
    const STATUS_KEEP_ENTERED_USERNAME_ASKED = 'keep_entered_username_asked';
    const STATUS_REGISTERED = 'registered';
    const TRANSITION_ASK_KEEP_TELEGRAM_USERNAME = 'ask_keep_telegram_username';
    const TRANSISTION_USING_TELEGRAM_USERNAME = 'using_telegram_username';
    const TRANSISTION_NOT_USING_TELEGRAM_USERNAME = 'not_using_telegram_username';
    const TRANSITION_ASK_ENTER_USERNAME = 'ask_enter_username';
    const TRANSITION_ASK_KEEP_ENTERED_USERNAME = 'ask_keep_entered_username';
    const TRANSITION_KEEPING_ENTERED_USERNAME = 'keeping_entered_username';
    const TRANSITION_NOT_KEEPING_ENTERED_USERNAME = 'not_keeping_entered_username';
    const REGEX_USERNAME = '/^[a-zA-Z0-9]{3,18}$/';

     public static function create(PrivateChat $chat){

        $registerFsm = new FiniteStateMachine($chat);
        $registerLoader = new ArrayLoader([
            'class'  => 'PrivateChat',
            'property_path' => 'registerstatus',
            'states' => [
                Registration::STATUS_UNREGISTERED => ['type' => 'initial'],
                Registration::STATUS_KEEP_TELEGRAM_USERNAME_ASKED => ['type' => 'normal'],
                Registration::STATUS_ENTER_USERNAME_ASKED => ['type' => 'normal'],
                Registration::STATUS_KEEP_ENTERED_USERNAME_ASKED => ['type' => 'normal'],
                Registration::STATUS_REGISTERED => ['type' => 'final'],
            ],
            'transitions' => [
                Registration::TRANSITION_ASK_KEEP_TELEGRAM_USERNAME => [
                    'from' => Registration::STATUS_UNREGISTERED,
                    'to'   => Registration::STATUS_KEEP_TELEGRAM_USERNAME_ASKED
                ],
                Registration::TRANSITION_ASK_ENTER_USERNAME => [
                    'from' => Registration::STATUS_UNREGISTERED,
                    'to'   => Registration::STATUS_ENTER_USERNAME_ASKED,
                ],
                Registration::TRANSISTION_NOT_USING_TELEGRAM_USERNAME => [
                    'from' => Registration::STATUS_KEEP_TELEGRAM_USERNAME_ASKED,
                    'to'   => Registration::STATUS_ENTER_USERNAME_ASKED
                ],
                Registration::TRANSISTION_USING_TELEGRAM_USERNAME => [
                    'from' => Registration::STATUS_KEEP_TELEGRAM_USERNAME_ASKED,
                    'to'   => Registration::STATUS_REGISTERED
                ],
                Registration::TRANSITION_ASK_KEEP_ENTERED_USERNAME => [
                    'from' => Registration::STATUS_ENTER_USERNAME_ASKED,
                    'to'   => Registration::STATUS_KEEP_ENTERED_USERNAME_ASKED
                ],
                Registration::TRANSITION_NOT_KEEPING_ENTERED_USERNAME => [
                    'from' => Registration::STATUS_KEEP_ENTERED_USERNAME_ASKED,
                    'to'   => Registration::STATUS_ENTER_USERNAME_ASKED
                ],
                Registration::TRANSITION_KEEPING_ENTERED_USERNAME => [
                    'from' => Registration::STATUS_KEEP_ENTERED_USERNAME_ASKED,
                    'to'   => Registration::STATUS_REGISTERED
                ]
            ]
        ]);
        $registerLoader->load($registerFsm);
        $registerFsm->initialize();
        return $registerFsm;

    }
}