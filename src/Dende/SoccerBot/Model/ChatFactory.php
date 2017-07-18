<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 29.06.2016
 * Time: 11:58
 */

namespace Dende\SoccerBot\Model;

use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            $chat = GroupChat::where('chat_id', '=', $chatId)->firstOrFail();
        } catch (ModelNotFoundException $e){}
        
        if (!is_null($chat)){
            return $chat;
        }
        
        try {
            $chat = PrivateChat::where('chat_id', '=', $chatId)->firstOrFail();
        } catch (ModelNotFoundException $e) {}

        if (!is_null($chat)) {
            $chat->restore();
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
                $chat->type = 'group';

            break;
            case "private":
                $chat = new PrivateChat();
                $chat->type = 'private';
                $chat->registerstatus = PrivateChat::REGISTER_STATUS_UNREGISTERED;
                break;
        }
        $chat->chat_id = $telegramChat->getId();
        $chat->liveticker = false;
        $chat->init();
        $chat->save();
        return $chat;
    }

}