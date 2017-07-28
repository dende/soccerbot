<?php


namespace Dende\SoccerBot\Command;

use Dende\SoccerBot\Exception\InvalidCommandStringException;
use Dende\SoccerBot\Repository\MatchRepository;
use Dende\SoccerBot\Repository\TeamRepository;
use \Telegram\Bot\Objects\Message as TelegramMessage;
use \Analog\Analog;
use Telegram\Bot\Objects\Update;


class CommandFactory
{

    /** @var MatchRepository */
    private $matchRepo;
    /** @var TeamRepository */
    private $teamRepo;

    public function __construct(MatchRepository $matchRepo, TeamRepository $teamRepo)
    {
        $this->matchRepo = $matchRepo;
        $this->teamRepo  = $teamRepo;
    }

    public function createFromString($commandString, $args = []){


        $classname = 'Dende\\SoccerBot\\Command\\'. ucfirst($commandString . 'Command');
        if (class_exists($classname, true)){
            /** @var AbstractCommand $command */
            $command = new $classname();
            $command->setArgs($args);
        } else {
            Analog::log("Command $classname not found", Analog::WARNING);
            $command = new NoopCommand();
            $command->setArgs($commandString);
        }

        // we have a command, give it its necessary repos
        switch (true){
            case $command instanceof RegisterCommand:

                break;
            case $command instanceof BetCommand || $command instanceof BetinfoCommand:
                $command->setMatchRepo($this->matchRepo);
                $command->setTeamRepo($this->teamRepo);
                break;
        }


        return $command;
    }

    private function commandStringFromMessage(TelegramMessage $message)
    {
        $entity = array_get($message->getRawResponse(), 'entities.0');

        if (array_get($entity, 'type') != 'bot_command'){
            throw new InvalidCommandStringException("Not a command");
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
            if ($parts[1] != TELEGRAM_API_USERNAME){
                throw new InvalidCommandStringException("Wrong Bot username");
            }
            $commandString = $parts[0];
        }

        return [$commandString, $args];
    }

    public function createFromUpdate(Update $update)
    {
        $message = $update->getMessage();
        try {
            list($commandString, $args) = $this->commandStringFromMessage($message);
            return $this->createFromString($commandString, $args);
        } catch (InvalidCommandStringException $e){
            return null;
        }

    }
}
