<?php
/**
 * Created by PhpStorm.
 * User: c
 * Date: 25.07.17
 * Time: 15:33
 */

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Chat;
use Dende\SoccerBot\Repository\MatchRepository;
use Dende\SoccerBot\Repository\TeamRepository;
use Telegram\Bot\Objects\Message as TelegramMessage;

abstract class AbstractCommand implements CommandInterface
{
    protected $args;
    /** @var  MatchRepository */
    protected $matchRepo;
    /** @var  TeamRepository */
    protected $teamRepo;

    function setArgs($args){
        $this->args = $args;
    }

    function getArgs(){
        return $this->args;
    }

    abstract function runPrivate(Chat $chat, TelegramMessage $message);
    abstract function runGroup(Chat $chat, TelegramMessage $message);

    function run(Chat $chat, TelegramMessage $message)
    {
        if ($chat->isPrivate()){
            return $this->runPrivate($chat, $message);
        } else {
            return $this->runGroup($chat, $message);
        }
    }

    function setMatchRepo(MatchRepository $matchRepo)
    {
        $this->matchRepo = $matchRepo;
    }

    function setTeamRepo(TeamRepository $teamRepo){
        $this->teamRepo = $teamRepo;
    }


}