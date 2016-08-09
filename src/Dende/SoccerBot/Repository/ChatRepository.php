<?php


namespace Dende\SoccerBot\Repository;


use Analog\Analog;
use Carbon\Carbon;
use Dende\SoccerBot\Model\Base\GroupChat;
use Dende\SoccerBot\Model\ChatInterface;
use Dende\SoccerBot\Model\FootballApi;
use Dende\SoccerBot\Model\Match;
use Dende\SoccerBot\Model\MatchQuery;
use Dende\SoccerBot\Model\PrivateChat;
use Dende\SoccerBot\Model\TeamQuery;
use GuzzleHttp\Client;

class ChatRepository
{

    public function live(ChatInterface $chat){
        /** @var $chat GroupChat|PrivateChat */
        $chat->setLiveticker(true);
        $chat->save();
    }

    public function mute(ChatInterface $chat){
        /** @var $chat GroupChat|PrivateChat */
        $chat->setLiveticker(true);
        $chat->save();
    }

}