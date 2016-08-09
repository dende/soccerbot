<?php


namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\ChatInterface;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;

class MuteCommand extends AbstractCommand
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
            $canTransit = $fsm->can('mute');
        } catch (\Finite\Exception\TransitionException $e){
            $canTransit = false;
        }

        if ($canTransit){
            $fsm->apply('mute', ['chat' => $chat, 'args' => $this->getArgs()]);
            $this->chatRepo->mute($chat);
            $response = new Message('command.mute.turnedOn');
        } else {
            if ($fsm->getCurrentState() == "muted"){
                $response = new Message('command.mute.alreadyOn');
            } else {
                $response = new Message('command.mute.cantTurnOn');
            }

        }

        return $response;

    }
}