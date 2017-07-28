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

    const REGEX_BET = '/^[0-9]{1,2}:[0-9]{1,2}$/';
    const INPUT_STOP = 'STOP';

    protected $tabletable = "bets";
    public $timestamps = false;

    public function getBetString()
    {
        return $this->home_team_goals . ':' . $this->away_team_goals;
    }

    public function match(){
        return $this->belongsTo('\Dende\SoccerBot\Model\Match');
    }

    public function chat(){
        return $this->belongsTo('\Dende\SoccerBot\Model\Chat');
    }
}
