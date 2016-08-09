<?php


namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\ChatInterface;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;

class LiveCommand extends AbstractCommand
{

    protected function runPrivate(PrivateChat $chat)
    {
        return $this->runBoth($chat);
    }

    protected function runGroup(GroupChat $chat)
    {
        return $this->runBoth($chat);
    }

    private function runBoth(ChatInterface $chat){
        $fsm = $chat->getFSM();
        try{
            $canTransit = $fsm->can('live');
        } catch (\Finite\Exception\TransitionException $e){
            $canTransit = false;
        }

        if ($canTransit){
            $fsm->apply('live', ['chat' => $chat, 'args' => $this->getArgs()]);
            $this->chatRepo->live($chat);
            $response = new Message('command.live.turnedOn');
        } else {
            if ($fsm->getCurrentState() == "liveticker"){
                $response = new Message('command.live.alreadyOn');
            } else {
                $response = new Message('command.live.cantTurnOn');
            }

        }

        return $response;

    }
}