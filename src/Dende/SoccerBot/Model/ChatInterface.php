<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 29.06.2016
 * Time: 11:55
 */

namespace Dende\SoccerBot\Model;

use Monolog\Logger;
use Symfony\Component\Translation\Translator;
use Telegram\Bot\Objects\Update as TelegramUpdate;


interface ChatInterface
{

    public function getChatId();

    public function init();
    
    public function restore();
    
    public function handle(TelegramUpdate $update);
}