<?php

namespace Dende\SoccerBot\Model;

use Dende\SoccerBot\Command\BetCommand;
use Dende\SoccerBot\Command\CommandFactory;
use Dende\SoccerBot\Command\RegisterCommand;
use Dende\SoccerBot\Model\FiniteStateMachine\Registration;
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

    const BET_STATUS_INACTIVE = 'inactive';
    const BET_STATUS_WAITING_FOR_BET = 'waiting_for_bet';

    const BET_TRANSITION_ASK_GOALS = 'ask_goals';
    const BET_TRANSITION_NEXT = 'next';
    const BET_TRANSITION_LATER = 'later';
    const BET_TRANSITION_DONE = 'done';

    const REGEX_BET = '/^[0-9]{1,2}:[0-9]{1,2}$/';

    /**
     *
     */
    public function init()
    {
        $this->registerFsm = Registration::create($this);

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

        //user is in registration process
        if ($this->registerstatus !== Registration::STATUS_UNREGISTERED &&
        $this->registerstatus !== Registration::STATUS_REGISTERED){
            $command = $commandFactory->createFromString("register");
            /** @var $command RegisterCommand */
            return $command->run($this, $message);
        }
        if ($this->betstatus === PrivateChat::BET_STATUS_WAITING_FOR_BET){
            $command = $commandFactory->createFromString("bet");
            /** @var BetCommand $command */
            return $command->run($this, $message);
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
