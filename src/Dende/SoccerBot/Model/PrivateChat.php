<?php

namespace Dende\SoccerBot\Model;

use Dende\SoccerBot\Model\Base\PrivateChat as BasePrivateChat;
use Finite\Loader\ArrayLoader;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine as FiniteStateMachine;
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
class PrivateChat extends BasePrivateChat implements StatefulInterface, ChatInterface
{
	public static $initialState = 'muted';
    /** @var  FiniteStateMachine */
    private $fsm;

    public function init()
    {
        $this->fsm = new FiniteStateMachine($this);
        $arrayLoader = new ArrayLoader([
            'class'  => 'PrivateChat',
            'states' => [
                'liveticker' => [
                    'type' => 'normal',
                    'properties' => []
                ],
                'muted' => [
                    'type' => 'initial',
                    'properties' => []
                ]
            ],
            'transitions' => [
                'live' => [
                    'from' => ['muted'],
                    'to'   => 'liveticker',
                    'properties' => ['chat' => null, 'args' => null]
                ],
                'mute' => [
                    'from' => ['liveticker'],
                    'to'   => 'muted',
                    'properties' => ['chat' => null, 'args' => null]
                ],
            ]
        ]);
        $arrayLoader->load($this->fsm);
        $this->fsm->initialize();
        $this->fsm->getDispatcher()->addListener('finite.post_transition.live', [$this, 'liveTransition']);
        $this->fsm->getDispatcher()->addListener('finite.post_transition.mute', [$this, 'muteTransition']);
    }
    
    public function restore(){
        $this->init();
        //TODO: logic for restoring should go here
    }

    /**
	 * Gets the object state.
	 *
	 * @return string
	 */
	public function getFiniteState()
	{
		return $this->state;
	}
	/**
	 * Sets the object state.
	 *
	 * @param string $state
	 */
	public function setFiniteState($state)
	{
		$this->state = $state;
	}

}
