<?php


namespace Dende\SoccerBot\Repository;


use Analog\Analog;
use Carbon\Carbon;
use Dende\SoccerBot\FootballData\Api as FootballDataApi;
use Dende\SoccerBot\Model\ChatInterface;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Telegram\Message as TelegramMessage;
use Dende\SoccerBot\Model\Chat;
use Dende\SoccerBot\Model\Team;
use Dende\SoccerBot\Telegram\Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Collection;

class MatchRepository
{
    private $footballDataApi;

    public function __construct(FootballDataApi $footballApi)
    {
        $this->footballDataApi = $footballApi;
        $this->init();
    }

    /**
     * @return array
     */
    public function update(){
        Analog::log("Updating matches");
        /** @var Match[] $timedMatches */
        $timedMatches = Match::where('status', '=', 'TIMED')
            ->orWhere('status', '=', 'IN_PLAY');


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
                    $message = new Response();

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
        $data = $this->footballDataApi->fetch($match->getUrl());
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
        $matchCount = Capsule::table('matches')->count();

        $rootData = $this->footballDataApi->getRootData();
        if ($matchCount < $rootData["numberOfGames"]){
            $uri = array_get($rootData, '_links.fixtures.href');
            if (empty($uri)){
                Analog::log('No Fixtures found', Analog::CRITICAL);
            }
            $data = $this->footballDataApi->fetch($uri);
            foreach ($data["fixtures"] as $fixtureData){
                $match = new Match();
                $match->home_team_id = Team::whereName($fixtureData["homeTeamName"])->first()->id;
                $match->away_team_id = Team::whereName($fixtureData["awayTeamName"])->first()->id;
                $match->date = new \DateTime($fixtureData["date"]);
                $match->date->setTimezone(new \DateTimeZone('Europe/Berlin'));
                $match->status = $fixtureData['status'];
                if ($match->status == null)
                    $match->status = Match::STATUS_SCHEDULED;
                $match->url = array_get($fixtureData, '_links.self.href');
                $match->save();
            }
            Analog::log('Updated the Matches in the database');
        } else {
            Analog::log('No updates for the matches needed');
        }
    }

    public function getOpenMatchesForChat(ChatInterface $chat){
        $nextMatches = Match::where('status', '=', Match::STATUS_SCHEDULED)->orWhere('status', '=', Match::STATUS_TIMED)->orderBy('date', 'desc')->get();

        //$bidMatches
        //todo exclude matches that are already in bets
        $openMatches = new Collection();

        foreach ($nextMatches as $match){
            //if (!$bidMatches->contains($match)){
            $openMatches->prepend($match);
            //}
        }

        return $openMatches;
    }
}