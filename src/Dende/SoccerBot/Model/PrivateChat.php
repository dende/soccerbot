<?php

namespace Dende\SoccerBot\Model;

use Dende\SoccerBot\Model\Base\PrivateChat as BasePrivateChat;
use Finite\Loader\ArrayLoader;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine as FiniteStateMachine;
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
class PrivateChat extends BasePrivateChat implements ChatInterface
{
    /** @var  FiniteStateMachine */
    private $registerFsm;

    const REGISTER_STATUS_UNREGISTERED = 'unregistered';
    const REGISTER_STATUS_KEEP_NAME_ASKED = 'keep_name_asked';
    const REGISTER_STATUS_NAME_ASKED = 'name_asked';
    const REGISTER_STATUS_REGISTERED = 'registered';

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
                'ask_keep_name' => [
                    'from' => 'unregistered',
                    'to'   => 'keep_name_asked'
                ],
                'ask_name' => [
                    'from' => 'unregistered',
                    'to'   => 'name_asked',
                ],
                'ask_new_name' => [
                    'from' => 'keep_name_asked',
                    'to'   => 'name_asked'
                ],
                'register' => [
                    'from' => 'name_asked',
                    'to'   => 'registered',
                ]
            ]
        ]);
        $registerLoader->load($this->registerFsm);
        $this->registerFsm->initialize();
    }
    
    public function restore(){
        $this->init();
        //TODO: logic for restoring should go here
    }

    public function handle(Message $mesage){
        if ($this->getRegisterstatus() === PrivateChat::REGISTER_STATUS_KEEP_NAME_ASKED){
            $fsm = $this->getRegisterFsm();
            ddd($fsm->getCurrentState());
        }

    }

    public function getRegisterFsm()
    {
        return $this->registerFsm;
    }

}
