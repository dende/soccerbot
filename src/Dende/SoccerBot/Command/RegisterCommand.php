<?php


namespace Dende\SoccerBot\Command;



use Dende\SoccerBot\Model\ChatInterface;
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
        if (empty($username)) {
            $fsm->apply(PrivateChat::REGISTER_TRANSITION_ASK_NAME);
            $chat->save();
            return new Message('command.register.transition_ask_name');
        } else {

            $keyboard = [
                [$lang->trans('general.yes'), $lang->trans('general.no')],
            ];

            $reply_markup = $telegram->replyKeyboardMarkup([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]);

            $telegram->sendMessage([
                'chat_id' => $chat->chat_id,
                'text' => $lang->trans('command.register.keepUsername', ['%username%' => $username]),
                'reply_markup' => $reply_markup,
                'parse_mode' => 'markdown'
            ]);

            $fsm->apply(PrivateChat::REGISTER_TRANSITION_ASK_KEEP_NAME);
            $chat->save();
        }
    }

    /**
     * @param ChatInterface $chat
     * @param TelegramMessage $message
     * @return Message|void
     */
    public function handleAnswer(ChatInterface $chat, TelegramMessage $message)
    {
        $lang = $this->chatRepo->getLang();
        if(!($chat instanceof PrivateChat)){
            return null;
        }

        $fsm = $chat->getRegisterFsm();
        $currentState = $fsm->getCurrentState()->getName();

        if ($currentState === PrivateChat::REGISTER_STATUS_KEEP_NAME_ASKED){
            switch($message->getText()){
                case $lang->trans('general.yes'):
                    $chat->setUsername($message->getChat()->getUsername());
                    $fsm->apply(PrivateChat::REGISTER_TRANSITION_KEEP_NAME);
                    $response = new Message('command.register.success', ['%username%' => $chat->getUsername()]);
                    break;
                case $lang->trans('general.no'):
                    $fsm->apply(PrivateChat::REGISTER_TRANSITION_ASK_NEW_NAME);
                    $response = new Message("command.register.transition_ask_name");
                    break;
                default:
                    $response = new Message('general.yesOrNoPls');
                    break;

            }
        } else if ($currentState === PrivateChat::REGISTER_STATUS_NAME_ASKED){
            $username = $message->getText();
            if (preg_match(PrivateChat::REGEX_USERNAME, $username)){
                $chat->setUsername($username);
                $fsm->apply(PrivateChat::REGISTER_TRANSITION_SET_NAME);
                $response = new Message('command.register.success', ['%username%' => $chat->getUsername()]);
            } else {
                $response = new Message('command.register.regex');
            }
        }
        $chat->save();
        /** @var Message $response */
        return $response;

    }


    protected function runGroup(GroupChat $chat, TelegramMessage $message)
    {
        return new Message('command.register.group');
    }
}