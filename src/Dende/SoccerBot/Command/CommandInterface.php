<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 24.06.2016
 * Time: 17:18
 */

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Chat;
use Dende\SoccerBot\Repository\MatchRepository;
use Dende\SoccerBot\Repository\TeamRepository;
use Telegram\Bot\Objects\Message as TelegramMessage;

interface CommandInterface
{

    public function run(Chat $chat, TelegramMessage $message);

    function setArgs($args);

    function getArgs();

    function setMatchRepo(MatchRepository $matchRepo);

    function setTeamRepo(TeamRepository $teamRepo);
}