<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 24.06.2016
 * Time: 17:19
 */

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Bet;
use Dende\SoccerBot\Model\FiniteStateMachine\Registration;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Telegram\Bot\Objects\Message as TelegramMessage;

class BetCommand extends AbstractCommand
{
    protected function runPrivate(PrivateChat $chat, TelegramMessage $message){
        if ($chat->registerstatus !== TelegramMessage::REGISTER_STATUS_REGISTERED){
            return new Message('command.bet.register');
        }

        $openMatches = $this->matchRepo->getOpenMatchesForChat($chat);

        if ($openMatches->isEmpty()){
            return new Message('command.bet.noMatches');
        }

        /** @var Match $match */
        $match = $openMatches->getFirst();

        $fsm = $chat->getBetFsm();
        $fsm->apply(PrivateChat::BET_TRANSITION_ASK_GOALS);

        $chat->current_bet_match_id = $match->id;
        $chat->save();

        $homeTeam = $match->getHomeTeam();
        $awayTeam = $match->getAwayTeam();

        return new Message('command.bet.yourBet', [
            '%homeTeamName%' => $homeTeam->getName(),
            '%awayTeamName%' => $awayTeam->getName(),
            '%date%' => $match->getDate('m.d.'),
        ]);

    }

    protected function runGroup(GroupChat $chat, TelegramMessage $message){
        return new Message('command.bet.group');
    }

    public function handleAnswer(PrivateChat $chat, TelegramMessage $message)
    {
        $text = $message->getText();
        if (!preg_match(PrivateChat::REGEX_BET, $text)) {
            return new Message('command.bet.regex');
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
            $response = new Message('command.bet.done');
        } else {
            /** @var Match $match */
            $match = $openMatches->getFirst();
            $fsm->apply(PrivateChat::BET_TRANSITION_NEXT);
            $chat->setCurrentBetMatch($match);
            $homeTeam = $match->getHomeTeam();
            $awayTeam = $match->getAwayTeam();
            $response = new Message('command.bet.yourBet', [
                '%homeTeamName%' => $homeTeam->getName(),
                '%awayTeamName%' => $awayTeam->getName(),
                '%date%' => $match->getDate('m.d.'),
            ]);
        }

        $chat->save();
        return $response;
    }
}