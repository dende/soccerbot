<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 29.06.2016
 * Time: 12:09
 */

namespace Dende\SoccerBot\Model;

use Finite\StateMachine\StateMachine as FiniteStateMachine;

class FiniteStateMachineFactory
{
    public function create(ChatInterface $chat, $states){
        $chatId = $chat->getChatId();
        $fsm = null;
        $privateChatLoader = new \Finite\Loader\ArrayLoader($this->config['FSM_PRIVATECHAT']);
        $groupChatLoader   = new \Finite\Loader\ArrayLoader($this->config['FSM_GROUPCHAT']);

        if ($chat instanceof PrivateChat){
            if (array_key_exists($chatId, $states)){
                $fsm = array_get($states, 'private.' . $chatId);
            } else {
                $fsm = new FiniteStateMachine($chat);
                $this->privateChatLoader->load($fsm);
                $fsm->initialize();
                $this->initPrivateFSM($fsm);
                $this->states['private'][$chatId] = $fsm;
            }
            $this->handlePrivateChat($chat, $update, $fsm);
        } else if ($chat instanceof GroupChat){
            if (array_key_exists($chatId, $this->states)){
                $fsm = array_get($this->states, 'group.' . $chatId);
            } else {
                $fsm = new FiniteStateMachine($chat);
                $this->groupChatLoader->load($fsm);
                $fsm->initialize();
                $this->initGroupFSM($fsm);
                array_set($this->states, 'group.' . $chatId, $fsm);
                $this->states['group'][$chatId] = $fsm;
            }
            $this->handleGroupChat($chat, $update, $fsm);
        }

    }
}