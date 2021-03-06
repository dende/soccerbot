<?php

namespace Dende\SoccerBot\Model;

use Dende\SoccerBot\Model\Base\Team as BaseTeam;
use Dende\SoccerBot\Helper;
use Illuminate\Database\Eloquent\Model;

/**
 * Skeleton subclass for representing a row from the 'teams' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Team extends Model
{
    protected $table = 'teams';
    public $timestamps = false;


}
