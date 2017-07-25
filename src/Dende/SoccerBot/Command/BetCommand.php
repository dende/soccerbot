<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 24.06.2016
 * Time: 17:19
 */

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Bet;
use Dende\SoccerBot\Model\FiniteStateMachine\RegistrationFSM;
use Dende\SoccerBot\Model\Chat;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Model\Telegram\Response;
use Telegram\Bot\Objects\Message as TelegramMessage;

class BetCommand extends AbstractCommand
{
     function run(Chat $chat, TelegramMessage $message){
        if ($chat->registerstatus !== RegistrationFSM::STATUS_REGISTERED){
            return new Response('command.bet.register');
        }

        $openMatches = $this->matchRepo->getOpenMatchesForChat($chat);

        if ($openMatches->isEmpty()){
            return new Response('command.bet.noMatches');
        }

        /** @var Match $match */
        $match = $openMatches->first();

        $fsm = $chat->getBetFsm();
        $fsm->apply(BetFsm::TRANSITION_ASK_GOALS);

        $chat->current_bet_match_id = $match->id;
        $chat->save();

        $homeTeam = $match->homeTeam()->get();
        $awayTeam = $match->awayTeam()->get();

        return new Response('command.bet.yourBet', [
            '%homeTeamName%' => $homeTeam->getName(),
            '%awayTeamName%' => $awayTeam->getName(),
            '%date%' => $match->getDate('m.d.'),
        ]);

    }

    protected function runGroup(GroupChat $chat, TelegramMessage $message){
        return new Response('command.bet.group');
    }

    public function handleAnswer(PrivateChat $chat, TelegramMessage $message)
    {
        $text = $message->getText();
        if (!preg_match(PrivateChat::REGEX_BET, $text)) {
            return new Response('command.bet.regex');
        }

        list($homeGoals, $awayGoals) = explode(':', $text);

        $bet = new Bet();
        $bet->setPrivateChat($chat);
        $bet->setMatch($chat->getCurrentBetMatch());
        $bet->setHomeTeamGoals($homeGoals);
        $bet->setAwayTeamGoals($awayGoals);
        $bet->save();

        $telegram = $this->chatRepo->getTelegramApi()->getTelegram();
        $lang = $this->chatRepo->getLang();
        $telegram->sendMessage([
            'chat_id' => $chat->chat_id,
            'text' => $lang->trans('command.bet.info', [
                '%homeTeamName%' => $bet->getMatch()->getHomeTeam()->getName(),
                '%awayTeamName%' => $bet->getMatch()->getAwayTeam()->getName(),
                '%bet%'          => $bet->getBetString()
            ]),
            'parse_mode' => 'markdown']);
        $chat->setCurrentBetMatch(null);
        $fsm = $chat->getBetFsm();

        $openMatches = $this->matchRepo->getOpenMatchesForChat($chat);

        if ($openMatches->isEmpty()){
            $fsm->apply(PrivateChat::BET_TRANSITION_DONE);
            $response = new Response('command.bet.done');
        } else {
            /** @var Match $match */
            $match = $openMatches->getFirst();
            $fsm->apply(PrivateChat::BET_TRANSITION_NEXT);
            $chat->setCurrentBetMatch($match);
            $homeTeam = $match->getHomeTeam();
            $awayTeam = $match->getAwayTeam();
            $response = new Response('command.bet.yourBet', [
                '%homeTeamName%' => $homeTeam->getName(),
                '%awayTeamName%' => $awayTeam->getName(),
                '%date%' => $match->getDate('m.d.'),
            ]);
        }

        $chat->save();
        return $response;
    }
}