<?php


namespace Dende\SoccerBot\Telegram;


use Analog\Analog;
use Dende\SoccerBot\Model\ChatInterface;
use Symfony\Component\Translation\Translator;


class Api
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
        $this->telegram = new \Telegram\Bot\Api(TELEGRAM_API_TOKEN);
    }

    public function sendMessage(ChatInterface $chat, Response $response){
        $keyboard = $response->getKeyboard();

        if (!is_null($keyboard)){
            $reply_markup = $this->telegram->replyKeyboardMarkup([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]);
        } else {
            $reply_markup = $this->telegram->replyKeyboardHide();
        }


        $this->telegram->sendMessage(['chat_id' => $chat->getChatId(), 'text' => $response->getText(), 'parse_mode' => 'Markdown', 'reply_markup' => $reply_markup]);
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

    public function respond(ChatInterface $chat, $response){
        $update = $chat->getCurrentUpdate();
        $this->sendMessage($chat, $response);
        $this->setOffset($update->getUpdateId() + 1);
    }
}
