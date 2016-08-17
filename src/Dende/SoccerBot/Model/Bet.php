<?php

namespace Dende\SoccerBot\Model;

use Dende\SoccerBot\Model\Base\Bet as BaseBet;

/**
 * Skeleton subclass for representing a row from the 'bets' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Bet extends BaseBet
{

    public function getBetString()
    {
        return $this->getHomeTeamGoals() . ':' . $this->getAwayTeamGoals();
    }
}
