<?php

namespace Dende\SoccerBot\Model;

use Dende\SoccerBot\Model\Base\PrivateChat as BasePrivateChat;
use Finite\StatefulInterface;

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
