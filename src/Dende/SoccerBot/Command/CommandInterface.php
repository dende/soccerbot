<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 24.06.2016
 * Time: 17:18
 */

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\ChatInterface;

interface CommandInterface
{
    public static function run(ChatInterface $chat);
}