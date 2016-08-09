<?php


namespace Dende\SoccerBot\Repository;


use Analog\Analog;
use Dende\SoccerBot\Model\Team;
use Dende\SoccerBot\Model\TeamQuery;
use Dende\SoccerBot\Model\FootballApi;

class TeamRepository
{
    private $footballApi;

    public function __construct(FootballApi $footballApi)
    {
        $this->footballApi = $footballApi;
    }

    public function init()
    {
        if (is_null(TeamQuery::create()->findPk(1))){
            $uri = array_get($this->footballApi->getRootData(), '_links.teams.href');
            if (empty($uri)){
                Analog::log('No Teams found', Analog::CRITICAL);
            }
            $data = $this->footballApi->fetch($uri);
            foreach (array_get($data, 'teams', []) as $teamData){
                $team = new Team();
                $team->setName(array_get($teamData, 'name'));
                $team->setCode(array_get($teamData, 'code'));
                $team->save();
            }
            Analog::log('Updated the Teams in the database');
        } else {
            Analog::log('No updates for the teams needed');
        }
    }
}