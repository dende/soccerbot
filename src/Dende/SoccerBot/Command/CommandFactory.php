<?php


namespace Dende\SoccerBot\Command;

use Dende\SoccerBot\Exception\CommandNotFoundException;
use Dende\SoccerBot\Exception\InvalidCommandStringException;
use \Telegram\Bot\Objects\Message as TelegramMessage;
use \Analog\Analog;


class CommandFactory
{
    public static function createFromString($commandString){

        if (class_exists(ucfirst($commandString . 'Command'))){
            return new $commandString();
        }
        Analog::log("Command for $commandString not found", Analog::WARNING);
        throw new CommandNotFoundException("CommandNotFound");
    }

    public static function commandStringFromMessage(TelegramMessage $message)
    {
        $entity = array_get($message->getRawResponse(), 'entities.0');

        if (array_get($entity, 'type') != 'bot_command'){
            return null;
        }

        $text = $message->getText();

        $commandString = substr($text, array_get($entity, 'offset'), array_get($entity, 'length'));
        $args = explode(' ', substr($text, array_get($entity, 'length')+1));
        if (substr($commandString,0,1) != '/'){
            throw new InvalidCommandStringException("Wrong kind of command");
        }
        $commandString = substr($commandString,1);

        if (str_contains($commandString, '@')){
            $parts  = explode('@', $commandString);
            if (count($parts) != 2){
                throw new InvalidCommandStringException("Wrong kind of command");
            }
            if ($parts[1] != TELEGRAM_API_USERNAME){
                throw new InvalidCommandStringException("Wrong Bot username");
            }
            $commandString = $parts[0];
        }

        return [$commandString, $args];
    }
}
