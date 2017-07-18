<?php


namespace Dende\SoccerBot\Model;


use Analog\Analog;
use Symfony\Component\Translation\Translator;
use Telegram\Bot\Api;


class TelegramApi
{
    /** @var Translator */
    private $lang;
    /** @var Api  */
    private $telegram;
    private $offset;

    public function __construct(Translator $lang)
    {
        $this->lang = $lang;
        $this->offset = 0;
        $this->telegram = new Api(TELEGRAM_API_TOKEN);
    }

    public function sendMessage($message, ChatInterface $chat){
        if (!empty($message) && $message instanceof Message){
            $this->telegram->sendMessage(['chat_id' => $chat->chat_id, 'text' => $message->translate($this->lang), 'parse_mode' => 'Markdown']);
        }
    }

    /**
     * @return \Telegram\Bot\Objects\Update[]
     */
    public function getUpdates(){
        Analog::log($this->offset, Analog::DEBUG);
        return $this->telegram->getUpdates([
            'offset' => $this->offset,
            'limit' => TELEGRAM_API_LIMIT,
            'timeout' => TELEGRAM_API_TIMEOUT
        ]);
    }

    public function getTelegram(){
        return $this->telegram;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

}
