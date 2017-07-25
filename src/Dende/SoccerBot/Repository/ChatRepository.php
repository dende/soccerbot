<?php


namespace Dende\SoccerBot\Repository;


use Dende\SoccerBot\Command\CommandFactory;
use Dende\SoccerBot\Model\Chat;
use Dende\SoccerBot\Model\FiniteStateMachine\RegistrationFSM;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Translation\Translator;
use Telegram\Bot\Objects\Update;

class ChatRepository
{
    /** @var Translator  */
    private $lang;

    public function __construct(Translator $lang)
    {
        $this->lang = $lang;
    }

    public function live(Chat $chat){
        $chat->liveticker = true;
        $chat->save();
    }

    public function mute(Chat $chat){
        $chat->liveticker = false;
        $chat->save();
    }

    /**
     * @return Chat[]
     */
    public function getLivetickerChats()
    {
        $chats = Chat::where('liveticker', '=', true)->get();

        return $chats;

    }

    public function createFromUpdate(Update $update){

        $telegramChat = $update->getMessage()->getChat();

        if (is_null($telegramChat))
            throw new Exception("Chat is empty");

        $chatId = $telegramChat->getId();
        $chat = null;


        try {
            /** @var Chat $chat */
            $chat = Chat::where('chat_id', '=', $chatId)->firstOrFail();
        } catch (ModelNotFoundException $e) {}

        if (!is_null($chat)) {
            $chat->restore($update);
            $chat->setLang($this->lang);
            return $chat;
        }

        $chatType = $telegramChat->getType();

        $chat = new Chat();
        $chat->setLang($this->lang);
        $chat->setCurrentUpdate($update);
        switch ($chatType){
            case "group":
            case "supergroup":
            case "channel":
            default:
                $chat->type = 'group';

                break;
            case "private":
                $chat->type = 'private';
                $chat->registerstatus = RegistrationFSM::STATUS_UNREGISTERED;
                break;
        }
        $chat->chat_id = $telegramChat->getId();
        $chat->liveticker = false;
        $chat->init();
        $chat->save();
        return $chat;
    }




}