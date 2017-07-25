<?php


namespace Dende\SoccerBot\Repository;


use Analog\Analog;
use Dende\SoccerBot\Model\Team;
use Dende\SoccerBot\FootballData\Api as FootballDataApi;
use Illuminate\Database\Capsule\Manager as Capsule;

class TeamRepository
{
    private $footballApi;

    public function __construct(FootballDataApi $footballApi)
    {
        $this->footballApi = $footballApi;
        $this->init();
    }

    public function init()
    {
        if (Capsule::table('teams')->count() == 0){
            $uri = array_get($this->footballApi->getRootData(), '_links.teams.href');
            if (empty($uri)){
                Analog::log('No Teams found', Analog::CRITICAL);
            }
            $data = $this->footballApi->fetch($uri);
            foreach (array_get($data, 'teams', []) as $teamData){
                $team = new Team();
                $team->name = array_get($teamData, 'name');
                $team->code = array_get($teamData, 'code');
                $team->save();
            }
            Analog::log('Updated the Teams in the database');
        } else {
            Analog::log('No updates for the teams needed');
        }
    }
}