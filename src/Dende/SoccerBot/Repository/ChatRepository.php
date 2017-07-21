<?php


namespace Dende\SoccerBot\Repository;


use Dende\SoccerBot\Model\ChatInterface;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\PrivateChat;
use Dende\SoccerBot\Model\Telegram\Api as TelegramApi;
use Symfony\Component\Translation\Translator;

class ChatRepository
{
    private $telegramApi;
    private $lang;

    public function __construct(TelegramApi $telegramApi, Translator $lang)
    {
        $this->telegramApi = $telegramApi;
        $this->lang = $lang;
    }

    public function live(ChatInterface $chat){
        /** @var $chat GroupChat|PrivateChat */
        $chat->setLiveticker(true);
        $chat->save();
    }

    public function mute(ChatInterface $chat){
        /** @var $chat GroupChat|PrivateChat */
        $chat->setLiveticker(false);
        $chat->save();
    }

    /**
     * @return TelegramApi
     */
    public function getTelegramApi(){
        return $this->telegramApi;
    }

    /**
     * @return ChatInterface[]
     */
    public function getLivetickerChats()
    {
        $groupChats = GroupChat::where('liveticker', '=', true)->get();
        $privateChats = PrivateChat::where('liveticker', '=', true)->get();

        return array_merge($groupChats->toArray(), $privateChats->toArray());

    }

    /**
     * @return Translator
     */
    public function getLang(){
        return $this->lang;
    }

}