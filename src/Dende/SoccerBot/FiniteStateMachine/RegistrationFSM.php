<?php

namespace Dende\SoccerBot\FiniteStateMachine;
use Dende\SoccerBot\Model\Chat;
use Finite\Loader\ArrayLoader;
use Finite\StateMachine\StateMachine as FiniteStateMachine;


class RegistrationFSM
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

     public static function create(Chat $chat){

        $registerFsm = new FiniteStateMachine($chat);
        $registerLoader = new ArrayLoader([
            'class'  => 'Chat',
            'property_path' => 'registerstatus',
            'states' => [
                RegistrationFSM::STATUS_UNREGISTERED => ['type' => 'initial'],
                RegistrationFSM::STATUS_KEEP_TELEGRAM_USERNAME_ASKED => ['type' => 'normal'],
                RegistrationFSM::STATUS_ENTER_USERNAME_ASKED => ['type' => 'normal'],
                RegistrationFSM::STATUS_KEEP_ENTERED_USERNAME_ASKED => ['type' => 'normal'],
                RegistrationFSM::STATUS_REGISTERED => ['type' => 'final'],
            ],
            'transitions' => [
                RegistrationFSM::TRANSITION_ASK_KEEP_TELEGRAM_USERNAME => [
                    'from' => RegistrationFSM::STATUS_UNREGISTERED,
                    'to'   => RegistrationFSM::STATUS_KEEP_TELEGRAM_USERNAME_ASKED
                ],
                RegistrationFSM::TRANSITION_ASK_ENTER_USERNAME => [
                    'from' => RegistrationFSM::STATUS_UNREGISTERED,
                    'to'   => RegistrationFSM::STATUS_ENTER_USERNAME_ASKED,
                ],
                RegistrationFSM::TRANSISTION_NOT_USING_TELEGRAM_USERNAME => [
                    'from' => RegistrationFSM::STATUS_KEEP_TELEGRAM_USERNAME_ASKED,
                    'to'   => RegistrationFSM::STATUS_ENTER_USERNAME_ASKED
                ],
                RegistrationFSM::TRANSISTION_USING_TELEGRAM_USERNAME => [
                    'from' => RegistrationFSM::STATUS_KEEP_TELEGRAM_USERNAME_ASKED,
                    'to'   => RegistrationFSM::STATUS_REGISTERED
                ],
                RegistrationFSM::TRANSITION_ASK_KEEP_ENTERED_USERNAME => [
                    'from' => RegistrationFSM::STATUS_ENTER_USERNAME_ASKED,
                    'to'   => RegistrationFSM::STATUS_KEEP_ENTERED_USERNAME_ASKED
                ],
                RegistrationFSM::TRANSITION_NOT_KEEPING_ENTERED_USERNAME => [
                    'from' => RegistrationFSM::STATUS_KEEP_ENTERED_USERNAME_ASKED,
                    'to'   => RegistrationFSM::STATUS_ENTER_USERNAME_ASKED
                ],
                RegistrationFSM::TRANSITION_KEEPING_ENTERED_USERNAME => [
                    'from' => RegistrationFSM::STATUS_KEEP_ENTERED_USERNAME_ASKED,
                    'to'   => RegistrationFSM::STATUS_REGISTERED
                ]
            ]
        ]);
        $registerLoader->load($registerFsm);
        $registerFsm->initialize();
        return $registerFsm;

    }
}