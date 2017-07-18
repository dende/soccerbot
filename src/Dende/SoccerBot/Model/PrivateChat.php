<?php

namespace Dende\SoccerBot\Model;

use Dende\SoccerBot\Command\BetCommand;
use Dende\SoccerBot\Command\CommandFactory;
use Dende\SoccerBot\Command\RegisterCommand;
use Finite\Loader\ArrayLoader;
use Finite\StateMachine\StateMachine as FiniteStateMachine;
use Illuminate\Database\Eloquent\Model;
use Telegram\Bot\Objects\Message;

/**
 * Skeleton subclass for representing a row from the 'privatechats' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class PrivateChat extends Model implements ChatInterface
{
    /** @var  FiniteStateMachine */
    private $registerFsm;
    /** @var  FiniteStateMachine */
    private $betFsm;

    protected $table = 'privatechats';
    public $timestamps = false;

    const REGISTER_STATUS_UNREGISTERED = 'unregistered';
    const REGISTER_STATUS_KEEP_TELEGRAM_USERNAME_ASKED = 'keep_telegram_username_asked';
    const REGISTER_STATUS_ENTER_USERNAME_ASKED = 'enter_username_asked';
    const REGISTER_STATUS_KEEP_ENTERED_USERNAME_ASKED = 'keep_entered_username_asked';
    const REGISTER_STATUS_REGISTERED = 'registered';

    const REGISTER_TRANSITION_ASK_KEEP_TELEGRAM_USERNAME = 'ask_keep_telegram_username';
    const REGISTER_TRANSITION_ASK_ENTER_USERNAME = 'ask_enter_username';
    const REGISTER_TRANSISTION_NOT_USING_TELEGRAM_USERNAME = 'not_using_telegram_username';
    const REGISTER_TRANSISTION_USING_TELEGRAM_USERNAME = 'using_telegram_username';
    const REGISTER_TRANSITION_ASK_KEEP_ENTERED_USERNAME = 'ask_keep_entered_username';
    const REGISTER_TRANSITION_NOT_KEEPING_ENTERED_USERNAME = 'not_keeping_entered_username';
    const REGISTER_TRANSITION_KEEPING_ENTERED_USERNAME = 'keeping_entered_username';

    const BET_STATUS_INACTIVE = 'inactive';
    const BET_STATUS_WAITING_FOR_BET = 'waiting_for_bet';

    const BET_TRANSITION_ASK_GOALS = 'ask_goals';
    const BET_TRANSITION_NEXT = 'next';
    const BET_TRANSITION_LATER = 'later';
    const BET_TRANSITION_DONE = 'done';

    const REGEX_USERNAME = '/^[a-zA-Z0-9]{3,18}$/';
    const REGEX_BET = '/^[0-9]{1,2}:[0-9]{1,2}$/';

    public function init()
    {
        $this->registerFsm = new FiniteStateMachine($this);
        $registerLoader = new ArrayLoader([
            'class'  => 'PrivateChat',
            'property_path' => 'registerstatus',
            'states' => [
                PrivateChat::REGISTER_STATUS_UNREGISTERED    => ['type' => 'initial'],
                PrivateChat::REGISTER_STATUS_KEEP_TELEGRAM_USERNAME_ASKED => ['type' => 'normal'],
                PrivateChat::REGISTER_STATUS_ENTER_USERNAME_ASKED => ['type' => 'normal'],
                PrivateChat::REGISTER_STATUS_KEEP_ENTERED_USERNAME_ASKED => ['type' => 'normal'],
                PrivateChat::REGISTER_STATUS_REGISTERED      => ['type' => 'final'],
            ],
            'transitions' => [
                PrivateChat::REGISTER_TRANSITION_ASK_KEEP_TELEGRAM_USERNAME => [
                    'from' => PrivateChat::REGISTER_STATUS_UNREGISTERED,
                    'to'   => PrivateChat::REGISTER_STATUS_KEEP_TELEGRAM_USERNAME_ASKED
                ],
                PrivateChat::REGISTER_TRANSITION_ASK_ENTER_USERNAME => [
                    'from' => PrivateChat::REGISTER_STATUS_UNREGISTERED,
                    'to'   => PrivateChat::REGISTER_STATUS_ENTER_USERNAME_ASKED,
                ],
                PrivateChat::REGISTER_TRANSISTION_NOT_USING_TELEGRAM_USERNAME => [
                    'from' => PrivateChat::REGISTER_STATUS_KEEP_TELEGRAM_USERNAME_ASKED,
                    'to'   => PrivateChat::REGISTER_STATUS_ENTER_USERNAME_ASKED
                ],
                PrivateChat::REGISTER_TRANSISTION_USING_TELEGRAM_USERNAME => [
                    'from' => PrivateChat::REGISTER_STATUS_KEEP_TELEGRAM_USERNAME_ASKED,
                    'to'   => PrivateChat::REGISTER_STATUS_REGISTERED
                ],
                PrivateChat::REGISTER_TRANSITION_ASK_KEEP_ENTERED_USERNAME => [
                    'from' => PrivateChat::REGISTER_STATUS_ENTER_USERNAME_ASKED,
                    'to'   => PrivateChat::REGISTER_STATUS_KEEP_ENTERED_USERNAME_ASKED
                ],
                PrivateChat::REGISTER_TRANSITION_NOT_KEEPING_ENTERED_USERNAME => [
                    'from' => PrivateChat::REGISTER_STATUS_KEEP_ENTERED_USERNAME_ASKED,
                    'to'   => PrivateChat::REGISTER_STATUS_ENTER_USERNAME_ASKED
                ],
                PrivateChat::REGISTER_TRANSITION_KEEPING_ENTERED_USERNAME => [
                    'from' => PrivateChat::REGISTER_STATUS_KEEP_ENTERED_USERNAME_ASKED,
                    'to'   => PrivateChat::REGISTER_STATUS_REGISTERED
                ]
            ]
        ]);
        $registerLoader->load($this->registerFsm);
        $this->registerFsm->initialize();

        $this->betFsm = new FiniteStateMachine($this);
        $betLoader = new ArrayLoader([
            'class' => 'PrivateChat',
            'property_path' => 'betstatus',
            'states' => [
                PrivateChat::BET_STATUS_INACTIVE => ['type' => 'initial'],
                PrivateChat::BET_STATUS_WAITING_FOR_BET => ['type' => 'normal'],
            ],
            'transitions' => [
                PrivateChat::BET_TRANSITION_ASK_GOALS => [
                    'from' => PrivateChat::BET_STATUS_INACTIVE,
                    'to' => PrivateChat::BET_STATUS_WAITING_FOR_BET,
                ],
                PrivateChat::BET_TRANSITION_DONE => [
                    'from' => PrivateChat::BET_STATUS_WAITING_FOR_BET,
                    'to' => PrivateChat::BET_STATUS_INACTIVE,
                ],
                PrivateChat::BET_TRANSITION_LATER => [
                    'from' => PrivateChat::BET_STATUS_WAITING_FOR_BET,
                    'to' => PrivateChat::BET_STATUS_INACTIVE
                ],
                PrivateChat::BET_TRANSITION_NEXT => [
                    'from' => PrivateChat::BET_STATUS_WAITING_FOR_BET,
                    'to' => PrivateChat::BET_STATUS_WAITING_FOR_BET,
                ]
            ]
        ]);
        $betLoader->load($this->betFsm);
        $this->betFsm->initialize();

    }
    
    public function restore(){
        $this->init();
        //TODO: logic for restoring should go here
    }

    public function handle(Message $message, CommandFactory $commandFactory){
        if ($this->registerstatus !== PrivateChat::REGISTER_STATUS_UNREGISTERED &&
        $this->registerstatus !== PrivateChat::REGISTER_STATUS_REGISTERED){
            $command = $commandFactory->createFromString("register");
            /** @var $command RegisterCommand */
            return $command->handleAnswer($this, $message);
        }
        if ($this->betstatus === PrivateChat::BET_STATUS_WAITING_FOR_BET){
            $command = $commandFactory->createFromString("bet");
            /** @var BetCommand $command */
            return $command->handleAnswer($this, $message);
        }

    }

    public function getRegisterFsm()
    {
        return $this->registerFsm;
    }

    public function getBetFsm(){
        return $this->betFsm;
    }

    public function getChatId()
    {
        // TODO: Implement getChatId() method.
    }
}
