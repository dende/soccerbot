<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 29.06.2016
 * Time: 11:58
 */

namespace Dende\SoccerBot\Model;

use Symfony\Component\Config\Definition\Exception\Exception;
use \Telegram\Bot\Objects\Chat as TelegramChat;


class ChatFactory
{
    public static function create(TelegramChat $telegramChat){

        if (is_null($telegramChat))
            throw new Exception("Chat is empty");
        
        $chatId = $telegramChat->getId();
        $chat = null;
        
        try{
            $chat = GroupChatQuery::create()->findOneByChatId($chatId);
        } catch (\Propel\Runtime\Exception\EntityNotFoundException $e){}
        
        if (!is_null($chat)){
            return $chat;
        }
        
        try {
            $chat = PrivateChatQuery::create()->findOneByChatId($chatId);
        } catch (\Propel\Runtime\Exception\EntityNotFoundException $e) {}

        if (!is_null($chat)) {
            return $chat;
        }

        return ChatFactory::newChat($telegramChat);

    }

    private static function newChat(TelegramChat $telegramChat) {
        $chatType = $telegramChat->getType();

        switch ($chatType){
            case "group":
            case "supergroup":
            case "channel":
            default:
                $chat = new GroupChat();
                break;
            case "private":
                $chat = new PrivateChat();
                break;
        }

        $chat->setChatId($telegramChat->getId());
        $chat->setType($chatType);
        $chat->setState($chat::$initialState);
        $chat->save();
        return $chat;
    }

}