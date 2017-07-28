<?php

namespace Dende\SoccerBot\Model;
use Illuminate\Database\Eloquent\Model;

/**
 * Skeleton subclass for representing a row from the 'matches' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Match extends Model
{

    //TODO: implement
    const STATUS_SCHEDULED = 'SCHEDULED';
    const STATUS_TIMED     = 'TIMED';
    const STATUS_IN_PLAY   = 'IN_PLAY';
    const STATUS_FINISHED  = 'FINISHED';
    const STATUS_CANCELED  = 'CANCELED';
    const STATUS_POSTPONED = 'POSTPONED';

    protected $table = "matches";
    public $timestamps = false;
    protected $attributes = [
        'home_team_goals' => 0,
        'away_team_goals' => 0
    ];

    protected $dates = [
        'date'
    ];


    public function homeTeam(){
        return $this->belongsTo('\Dende\SoccerBot\Model\Team', 'home_team_id');
    }

    public function awayTeam(){
        return $this->belongsTo('\Dende\SoccerBot\Model\Team', 'away_team_id');
    }

    public function bets(){
        return $this->hasMany('Bet');
    }
}
