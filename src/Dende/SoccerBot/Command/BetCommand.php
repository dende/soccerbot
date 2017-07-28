<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 24.06.2016
 * Time: 17:19
 */

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Bet;
use Dende\SoccerBot\FiniteStateMachine\RegistrationFSM;
use Dende\SoccerBot\FiniteStateMachine\BetFSM;
use Dende\SoccerBot\Model\Chat;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Telegram\Response;
use Telegram\Bot\Objects\Message as TelegramMessage;

class BetCommand extends AbstractCommand
{
    public function runGroup(Chat $chat, TelegramMessage $message){
        return new Response('command.bet.group');
    }


    function runPrivate(Chat $chat, TelegramMessage $message)
    {

        if ($chat->registerstatus !== RegistrationFSM::STATUS_REGISTERED){
            return new Response($chat->getLang()->trans('command.bet.register'));
        }

        switch ($chat->betstatus){
            case BetFSM::STATUS_INACTIVE:
                return $this->askForGoals($chat, $message);
            case BetFSM::STATUS_GOALS_ASKED:
                return $this->processInput($chat, $message);
        }

    }

    private function askForGoals(Chat $chat, TelegramMessage $message)
    {
        $lang = $chat->getLang();
        $openMatches = $this->matchRepo->getOpenMatchesForChat($chat);

        if ($openMatches->isEmpty()){
            return new Response($lang->trans('command.bet.noMatches'));
        }

        /** @var Match $match */
        $match = $openMatches->first();

        $fsm = $chat->getBetFsm();
        $fsm->apply(BetFsm::TRANSITION_ASK_GOALS);

        $chat->current_bet_match_id = $match->id;
        $chat->save();

        $homeTeam = $match->homeTeam;
        $awayTeam = $match->awayTeam;

        $keyboard = [
            ['0:0', '0:1', '1:0'],
            ['1:1', '1:2', '2:1'],
            ['2:2', '2:3', '3:2'],
            ['STOP']
        ];
        return new Response($lang->trans('command.bet.yourBet', [
            '%homeTeamName%' => $homeTeam->name,
            '%awayTeamName%' => $awayTeam->name,
            '%date%' => $match->date->format('d.m.Y'),
        ]), $keyboard);

    }

    private function processInput(Chat $chat, TelegramMessage $message)
    {
        $lang = $chat->getLang();
        $text = $message->getText();
        $fsm = $chat->getBetFsm();


        if (strtoupper(trim($text)) == Bet::INPUT_STOP){
            $fsm->apply(BetFSM::TRANSITION_DONE);
            $response = new Response($lang->trans('command.bet.stopped'));
        } else if (preg_match(Bet::REGEX_BET, $text)) {

            list($homeGoals, $awayGoals) = explode(':', $text);

            $bet = new Bet();
            $bet->chat_id = $chat->id;
            $bet->match_id = $chat->currentBetMatch->id;
            $bet->home_team_goals = $homeGoals;
            $bet->away_team_goals = $awayGoals;

            try {
                $bet->save();
            } catch (\Exception $e){
                \Analog::log($e->getMessage(), \Analog::ERROR);
                return new Response($lang->trans('command.bet.failed'));
            }

            $response = new Response(
                $lang->trans('command.bet.info', [
                    '%homeTeamName%' => $bet->match->homeTeam->name,
                    '%awayTeamName%' => $bet->match->awayTeam->name,
                    '%bet%'          => $bet->getBetString()
                ]));
            $chat->current_bet_match_id = null;

            $openMatches = $this->matchRepo->getOpenMatchesForChat($chat);
            if ($openMatches->isEmpty()){
                $fsm->apply(BetFSM::TRANSITION_DONE);
                $response->addLine($lang->trans('command.bet.done'));
            } else {
                /** @var Match $match */
                $match = $openMatches->first();
                $fsm->apply(BetFSM::TRANSITION_NEXT);
                $chat->current_bet_match_id = $match->id;
                $homeTeam = $match->homeTeam;
                $awayTeam = $match->awayTeam;
                $response->addLine($lang->trans('command.bet.yourBet', [
                    '%homeTeamName%' => $homeTeam->name,
                    '%awayTeamName%' => $awayTeam->name,
                    '%date%' => $match->date->format('d.m.Y'),
                ]));
                $keyboard = [
                    ['0:0', '0:1', '1:0'],
                    ['1:1', '1:2', '2:1'],
                    ['2:2', '2:3', '3:2'],
                    ['STOP']
                ];
                $response->setKeyboard($keyboard);
            }
        } else {
            $response =  new Response($lang->trans('command.bet.regex'));
        }
        $chat->save();
        return $response;
    }
}