<?php


namespace Dende\SoccerBot\Command;



use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Telegram\Bot\Objects\Message as TelegramMessage;

class RegisterCommand extends AbstractCommand
{

    protected function runPrivate(PrivateChat $chat, TelegramMessage $message)
    {
        $fsm = $chat->getRegisterFsm();
        if ($fsm->getCurrentState()->getName() != PrivateChat::REGISTER_STATUS_UNREGISTERED){
            return new Message('command.register.cantRegister');
        }

        $chatRepo = $this->chatRepo;
        $lang     = $chatRepo->getLang();
        $telegram = $this->chatRepo->getTelegramApi()->getTelegram();


        $username = $message->getChat()->getUsername();
        if (!empty($username)){
            $keyboard = [
                [$lang->trans('general.yes')],
                [$lang->trans('general.no')],
            ];

            $reply_markup = $telegram->replyKeyboardMarkup([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]);

            $telegram->sendMessage([
                'chat_id' => $chat->getChatId(),
                'text' => $lang->trans('command.register.keepUsername', ['%username%' => $username]),
                'reply_markup' => $reply_markup
            ]);

            $fsm->apply('ask_keep_name');
            $chat->save();
        }
    }

    protected function runGroup(GroupChat $chat, TelegramMessage $message)
    {
        return new Message('command.register.group');
    }
}