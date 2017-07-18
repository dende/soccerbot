<?php

namespace Dende\SoccerBot\Model;

use Dende\SoccerBot\Model\Base\GroupChat as BaseGroupChat;
use Finite\Loader\ArrayLoader;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine as FiniteStateMachine;
use Illuminate\Database\Eloquent\Model;

/**
 * Skeleton subclass for representing a row from the 'groupchats' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class GroupChat extends Model implements StatefulInterface, ChatInterface
{
    /** @var  FiniteStateMachine */
    private $fsm;
    protected $state;
    protected $table = 'groupchats';
    public $timestamps = false;


    public function init()
    {
        $this->fsm = new FiniteStateMachine($this);
        $arrayLoader = new ArrayLoader([
            'class'  => 'GroupChat',
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

    public function getFSM()
    {
        return $this->fsm;
    }

    public function getChatId()
    {
        // TODO: Implement getChatId() method.
    }
}
