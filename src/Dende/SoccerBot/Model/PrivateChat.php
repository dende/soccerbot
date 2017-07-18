<?php

namespace Dende\SoccerBot\Model;

use Dende\SoccerBot\Command\BetCommand;
use Dende\SoccerBot\Command\CommandFactory;
use Dende\SoccerBot\Command\RegisterCommand;
use Dende\SoccerBot\Model\Base\PrivateChat as BasePrivateChat;
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
    const REGISTER_STATUS_KEEP_NAME_ASKED = 'keep_name_asked';
    const REGISTER_STATUS_NAME_ASKED = 'name_asked';
    const REGISTER_STATUS_REGISTERED = 'registered';

    const REGISTER_TRANSITION_ASK_KEEP_NAME = 'ask_keep_name';
    const REGISTER_TRANSITION_ASK_NAME = 'ask_name';
    const REGISTER_TRANSITION_ASK_NEW_NAME = 'ask_new_name';
    const REGISTER_TRANSITION_KEEP_NAME = 'keep_name';
    const REGISTER_TRANSITION_SET_NAME = 'set_name';

    const BET_STATUS_INACTIVE = 'inactive';
    const BET_STATUS_GOALS_ASKED = 'goals_asked';

    const BET_TRANSITION_ASK_GOALS = 'ask_goals';
    const BET_TRANSITION_NEXT = 'next';
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
                PrivateChat::REGISTER_STATUS_KEEP_NAME_ASKED => ['type' => 'normal'],
                PrivateChat::REGISTER_STATUS_NAME_ASKED      => ['type' => 'normal'],
                PrivateChat::REGISTER_STATUS_REGISTERED      => ['type' => 'final'],
            ],
            'transitions' => [
                PrivateChat::REGISTER_TRANSITION_ASK_KEEP_NAME => [
                    'from' => PrivateChat::REGISTER_STATUS_UNREGISTERED,
                    'to'   => PrivateChat::REGISTER_STATUS_KEEP_NAME_ASKED
                ],
                PrivateChat::REGISTER_TRANSITION_ASK_NAME => [
                    'from' => PrivateChat::REGISTER_STATUS_UNREGISTERED,
                    'to'   => PrivateChat::REGISTER_STATUS_NAME_ASKED,
                ],
                PrivateChat::REGISTER_TRANSITION_ASK_NEW_NAME => [
                    'from' => PrivateChat::REGISTER_STATUS_KEEP_NAME_ASKED,
                    'to'   => PrivateChat::REGISTER_STATUS_NAME_ASKED
                ],
                PrivateChat::REGISTER_TRANSITION_KEEP_NAME => [
                    'from' => PrivateChat::REGISTER_STATUS_KEEP_NAME_ASKED,
                    'to'   => PrivateChat::REGISTER_STATUS_REGISTERED,
                ],
                PrivateChat::REGISTER_TRANSITION_SET_NAME => [
                    'from' => PrivateChat::REGISTER_STATUS_NAME_ASKED,
                    'to'   => PrivateChat::REGISTER_STATUS_REGISTERED,
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
                PrivateChat::BET_STATUS_INACTIVE => ['type' => 'normal'],
                PrivateChat::BET_STATUS_GOALS_ASKED => ['type' => 'normal'],
            ],
            'transitions' => [
                PrivateChat::BET_TRANSITION_ASK_GOALS => [
                    'from' => PrivateChat::BET_STATUS_INACTIVE,
                    'to' => PrivateChat::BET_STATUS_GOALS_ASKED,
                ],
                PrivateChat::BET_TRANSITION_DONE => [
                    'from' => PrivateChat::BET_STATUS_GOALS_ASKED,
                    'to' => PrivateChat::BET_STATUS_INACTIVE,
                ],
                PrivateChat::BET_TRANSITION_NEXT => [
                    'from' => PrivateChat::BET_STATUS_GOALS_ASKED,
                    'to' => PrivateChat::BET_STATUS_GOALS_ASKED,
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
        if ($this->getRegisterstatus() !== PrivateChat::REGISTER_STATUS_UNREGISTERED &&
        $this->getRegisterstatus() !== PrivateChat::REGISTER_STATUS_REGISTERED){
            $command = $commandFactory->createFromString("register");
            /** @var $command RegisterCommand */
            return $command->handleAnswer($this, $message);
        }
        if ($this->getBetstatus() === PrivateChat::BET_STATUS_GOALS_ASKED){
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
