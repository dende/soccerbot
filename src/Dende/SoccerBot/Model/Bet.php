<?php

namespace Dende\SoccerBot\Model;
use Illuminate\Database\Eloquent\Model;

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
class Bet extends Model
{
    protected $tabletable = "bets";
    public $timestamps = false;

    public function getBetString()
    {
        return $this->getHomeTeamGoals() . ':' . $this->getAwayTeamGoals();
    }
}
