<?php


namespace Dende\SoccerBot\Command;

use Dende\SoccerBot\Exception\CommandNotFoundException;
use Dende\SoccerBot\Exception\InvalidCommandStringException;
use \Telegram\Bot\Objects\Message as TelegramMessage;
use \Analog\Analog;


class CommandFactory
{
    public static function createFromString($commandString, $args){

        $classname = 'Dende\\SoccerBot\\Command\\'. ucfirst($commandString . 'Command');
        if (class_exists($classname, true)){
            /** @var AbstractCommand $instance */
            $instance = new $classname($args);
        } else {
            Analog::log("Command $classname not found", Analog::WARNING);
            $instance = new NoopCommand($commandString);

        }
        return $instance;
        //throw new CommandNotFoundException("CommandNotFound");
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
        if (count($args) == 1 && empty($args[0])){
            $args = null;
        }
        if (substr($commandString,0,1) != '/'){
            throw new InvalidCommandStringException("Wrong kind of command");
        }
        $commandString = substr($commandString,1);

        if (str_contains($commandString, '@')){
            $parts  = explode('@', $commandString);
            if (count($parts) != 2){
                throw new InvalidCommandStringException("Wrong kind of command");
            }
            if ($parts[1] != TELEGRAM_BOT_USERNAME){
                throw new InvalidCommandStringException("Wrong Bot username");
            }
            $commandString = $parts[0];
        }

        return [$commandString, $args];
    }

    public static function createFromMessage(TelegramMessage $message)
    {
        list($commandString, $args) = CommandFactory::commandStringFromMessage($message);
        return CommandFactory::createFromString($commandString, $args);

    }
}
