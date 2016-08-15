<?php


namespace Dende\SoccerBot\Repository;


use Analog\Analog;
use Carbon\Carbon;
use Dende\SoccerBot\Model\Base\GroupChat;
use Dende\SoccerBot\Model\Base\GroupChatQuery;
use Dende\SoccerBot\Model\Base\PrivateChatQuery;
use Dende\SoccerBot\Model\ChatInterface;
use Dende\SoccerBot\Model\FootballApi;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Model\MatchQuery;
use Dende\SoccerBot\Model\PrivateChat;
use Dende\SoccerBot\Model\TeamQuery;
use Dende\SoccerBot\Model\TelegramApi;
use GuzzleHttp\Client;
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
        $groupChats = GroupChatQuery::create()->filterByLiveticker(true)->find();
        $privateChats = PrivateChatQuery::create()->filterByLiveticker(true)->find();

        return array_merge($groupChats->toArray(), $privateChats->toArray());

    }

    /**
     * @return Translator
     */
    public function getLang(){
        return $this->lang;
    }

}