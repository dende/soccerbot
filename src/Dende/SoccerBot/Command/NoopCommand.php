<?php


namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\ChatInterface;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Finite\State\StateInterface;

class NoopCommand extends AbstractCommand
{
    protected function runPrivate(PrivateChat $chat){
        return $this->runBoth($chat);
    }

    protected function runGroup(GroupChat $chat){
        return $this->runBoth($chat);
    }

    private function runBoth(ChatInterface $chat){
        return new Message('command.noop', ['%command%' => $this->args]);
        //NOOP LOL
        /*
        try{
            $canTransit = $this->fsm->can($commandString);
        } catch (\Finite\Exception\TransitionException $e){
            $canTransit = false;
        }

        $response = null;

        if ($canTransit){

            $response = $this->fsm->apply($commandString, ['chat' => $this, 'args' => $args]);

        } else {

            $state = $this->fsm->getCurrentState();

            try {
                $command = CommandFactory::createFromString($commandString);
                $response = $command->run($this, $args, $state);
            } catch (CommandNotFoundException $e){
                Analog::log('cannot transit or execute command ' . $commandString, Analog::WARNING);
            }

        }
        */

    }

}