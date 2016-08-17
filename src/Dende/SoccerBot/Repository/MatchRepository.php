<?php


namespace Dende\SoccerBot\Repository;


use Analog\Analog;
use Carbon\Carbon;
use Dende\SoccerBot\Model\FootballApi;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Model\MatchQuery;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Dende\SoccerBot\Model\TeamQuery;
use GuzzleHttp\Client;
use Propel\Runtime\Collection\ObjectCollection;

class MatchRepository
{
    private $footballApi;

    public function __construct(FootballApi $footballApi)
    {
        $this->footballApi = $footballApi;
        $this->init();
    }

    /**
     * @return Message[]
     */
    public function update(){
        Analog::log("Updating matches");
        /** @var Match[] $timedMatches */
        $timedMatches = MatchQuery::create()
            ->where('matches.status = ?', 'TIMED')
            ->_or()
            ->where('matches.status = ?', 'IN_PLAY')
            ->find();


        $messages = [];

        foreach ($timedMatches as $match){
            $date = Carbon::instance($match->getDate());
            $diff = Carbon::now()->diffInMinutes($date, false);

            if ($diff <= 0 && $diff >= -200){
                //these games started less than 200 minutes ago
                $info = null;
                try {
                    $info = $this->updateMatch($match);
                } catch (\GuzzleHttp\Exception\ServerException $e){
                    //football-api.org 500ed
                    continue;
                } catch (\GuzzleHttp\Exception\BadResponseException $e){
                    //some other bad response
                    continue;
                } catch (\Exception $e){
                    //can this even happen?
                    continue;
                }

                if (!is_null($info)){
                    $message = new Message();

                    $homeTeam = $match->getHomeTeam();
                    $awayTeam = $match->getAwayTeam();


                    if (array_get($info, 'status') == 'IN_PLAY'){
                        $message->addLine(
                            'live.matchStarted',
                            [
                                '%homeTeamName%'  => $homeTeam->getName(),
                                '%awayTeamName%'  => $awayTeam->getName(),
                            ]
                        );
                    }

                    if (array_has($info, 'homeTeamGoalsScored')){
                        $goalsScored         = array_get($info, 'homeTeamGoalsScored');
                        $teamScoredName      = $homeTeam->getName();
                        $teamConcededName    = $awayTeam->getName();
                    } else if (array_has($info, 'awayTeamGoalsScored')){
                        $goalsScored         = array_get($info, 'awayTeamGoalsScored');
                        $teamScoredName      = $awayTeam->getName();
                        $teamConcededName    = $homeTeam->getName();
                    }

                    if (!empty($goalsScored)){
                        /** @noinspection PhpUndefinedVariableInspection */
                        $message->addLine(
                            'live.teamScored',
                            [
                                '%teamScoredName%'   => $teamScoredName,
                                '%teamConcededName%' => $teamConcededName,
                                '%goals%'            => $goalsScored
                            ],
                            $goalsScored
                        );
                        $message->addLine(
                            'live.newScore',
                            [
                                '%homeTeamGoals%' => $match->getHomeTeamGoals(),
                                '%awayTeamGoals%' => $match->getAwayTeamGoals(),
                            ]
                        );
                    }

                    if (array_get($info, 'status') == 'FINISHED'){
                        $message->addLine(
                            'live.finished',
                            [
                                '%homeTeamName%' => $homeTeam->getName(),
                                '%awayTeamName%' => $awayTeam->getName()
                            ]
                        );
                        $message->addLine(
                            'live.finalScore',
                            [
                                '%homeTeamGoals%' => $match->getHomeTeamGoals(),
                                '%awayTeamGoals%' => $match->getAwayTeamGoals(),
                            ]
                        );
                    }
                    $messages[] = $message;

                }
            }
        }
        return $messages;
    }

    private function updateMatch(Match $match){
        $newData = [];
        $data = $this->footballApi->fetch($match->getUrl());
        $matchData = $data["fixture"];

        $oldStatus        = $match->getStatus();
        $newStatus        = array_get($matchData, 'status');
        $oldHomeTeamGoals = $match->getHomeTeamGoals();
        $newHomeTeamGoals = array_get($matchData, 'result.goalsHomeTeam');
        $oldAwayTeamGoals = $match->getAwayTeamGoals();
        $newAwayTeamGoals = array_get($matchData, 'result.goalsAwayTeam');

        if ($oldStatus != $newStatus){
            $newData['status'] = $newStatus;
            $match->setStatus($newStatus);
        }
        if ($oldHomeTeamGoals != $newHomeTeamGoals){
            $newData['homeTeamGoalsScored'] = $newHomeTeamGoals - $oldHomeTeamGoals;
            $match->setHomeTeamGoals($newHomeTeamGoals);
        }
        if ($oldAwayTeamGoals != $newAwayTeamGoals){
            $newData['awayTeamGoalsScored'] = $newAwayTeamGoals - $oldAwayTeamGoals;
            $match->setAwayTeamGoals($newAwayTeamGoals);
        }

        $match->save();

        return (empty($newData)?null:$newData);
    }

    public function init(){
        $matchCount = MatchQuery::create()->count();

        $rootData = $this->footballApi->getRootData();
        if ($matchCount < $rootData["numberOfGames"]){
            $uri = array_get($rootData, '_links.fixtures.href');
            if (empty($uri)){
                Analog::log('No Fixtures found', Analog::CRITICAL);
            }
            $data = $this->footballApi->fetch($uri);
            foreach ($data["fixtures"] as $fixtureData){
                $match = new Match();
                $homeTeam = TeamQuery::create()->findOneByName($fixtureData["homeTeamName"]);
                if (is_null($homeTeam)){
                    Analog::log('Home Team is null', Analog::CRITICAL);
                }
                $match->setHomeTeam($homeTeam);
                $awayTeam = TeamQuery::create()->findOneByName($fixtureData["awayTeamName"]);
                if (is_null($awayTeam)){
                    Analog::log('Away Team is null', Analog::CRITICAL);
                }
                $match->setAwayTeam($awayTeam);
                $date = new \DateTime($fixtureData["date"]);
                $date->setTimezone(new \DateTimeZone('Europe/Berlin'));
                $match->setDate($date);
                $match->setStatus($fixtureData["status"]);
                $match->setUrl(array_get($fixtureData, '_links.self.href'));
                $match->save();
            }
            Analog::log('Updated the Matches in the database');
        } else {
            Analog::log('No updates for the matches needed');
        }
    }

    public function getOpenMatchesForChat(PrivateChat $chat){
        $nextMatches = MatchQuery::create()->where('matches.status = ?', 'TIMED')->orderByDate()->find();
        $bidMatches  = MatchQuery::create()->useBetQuery()->filterByPrivateChat($chat)->find();

        $collection = new ObjectCollection();
        foreach ($nextMatches as $match){
            if (!$bidMatches->contains($match)){
                $collection->append($match);
            }
        }

        return $collection;
    }
}