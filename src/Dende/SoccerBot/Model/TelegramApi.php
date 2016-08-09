<?php


namespace Dende\SoccerBot\Model;


use Dende\SoccerBot\Command\CommandFactory;
use Symfony\Component\Translation\Translator;
use Telegram\Bot\Api;

class TelegramApi
{
    private $telegram;
    private $offset;

    public function __construct(Translator $lang)
    {
        $this->offset = 0;
        $this->telegram = new Api(TELEGRAM_BOT_TOKEN);
        $this->lang = $lang;
    }

    public function sendMessage($message, ChatInterface $chat){
        /** @var message $message */
        if (!empty($message)){
            $this->telegram->sendMessage(['chat_id' => $chat->getChatId(), 'text' => $message->translate($this->lang), 'parse_mode' => 'Markdown']);
        }
    }

    public function update(){
        $updates = $this->telegram->getUpdates([
            'offset' => $this->offset,
            'limit' => TELEGRAM_BOT_LIMIT,
            'timeout' => TELEGRAM_BOT_TIMEOUT
        ]);

        foreach ($updates as $update){
            try {
                $this->offset = $update->getUpdateId() + 1;

                $message = $update->getMessage();

                if(!$message){
                    throw new \Dende\SoccerBot\Exception\EmptyMessageException();
                }

                $chat = ChatFactory::create($message->getChat());


                $command = CommandFactory::createFromMessage($message);
                $response = $command->run($chat);

                $this->sendMessage($response, $chat);

            } catch (\Dende\SoccerBot\Exception\EmptyMessageException $e){
                //not too bad
            } catch (\Dende\SoccerBot\Exception\CommandNotFoundException $e){
                //not too bad
            } catch (\Dende\SoccerBot\Exception\InvalidCommandStringException $e){
                //not too bad
            }
        }

    }

}