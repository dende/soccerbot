<?php


namespace Dende\SoccerBot\Command;



use Dende\SoccerBot\Model\FiniteStateMachine\Registration;
use Dende\SoccerBot\Model\GroupChat;
use Dende\SoccerBot\Model\Telegram\Message;
use Dende\SoccerBot\Model\PrivateChat;
use Telegram\Bot\Objects\Message as TelegramMessage;

class RegisterCommand extends AbstractCommand
{

    //TODO: Check duplicate usernames
    protected function runPrivate(PrivateChat $chat, TelegramMessage $message)
    {
        $fsm = $chat->getRegisterFsm();
        $chatRepo = $this->chatRepo;
        $lang     = $chatRepo->getLang();
        $telegram = $this->chatRepo->getTelegramApi()->getTelegram();


        $username = $message->getChat()->getUsername();

        $fsm = $chat->getRegisterFsm();
        $currentRegisterState = $fsm->getCurrentState()->getName();

        switch($currentRegisterState){
            case Registration::STATUS_UNREGISTERED:
                if (empty($username)) {
                    $response = new Message("command.register.transition_ask_name");
                    $fsm->apply(Registration::TRANSITION_ASK_ENTER_USERNAME);
                } else {

                    $keyboard = [
                        [$lang->trans('general.yes'), $lang->trans('general.no')],
                    ];

                    $reply_markup = $telegram->replyKeyboardMarkup([
                        'keyboard' => $keyboard,
                        'resize_keyboard' => true,
                        'one_time_keyboard' => true
                    ]);

                    $response = new Message('command.register.keepUsername', ['%username%' => $username], false, $reply_markup);

                    $fsm->apply(Registration::TRANSITION_ASK_KEEP_TELEGRAM_USERNAME);
                }
                break;
            case Registration::STATUS_KEEP_TELEGRAM_USERNAME_ASKED:
                switch($message->getText()) {
                    case $lang->trans('general.yes'):
                        $chat->username = $message->getChat()->getUsername();
                        $fsm->apply(Registration::TRANSISTION_USING_TELEGRAM_USERNAME);
                        $response = new Message('command.register.success', ['%username%' => $chat->username]);
                        break;
                    case $lang->trans('general.no'):
                        $fsm->apply(Registration::TRANSISTION_NOT_USING_TELEGRAM_USERNAME);
                        $response = new Message("command.register.transition_ask_name");
                        break;
                    default:
                        $response = new Message('general.yesOrNoPls');
                        break;
                }
                break;
            case Registration::STATUS_ENTER_USERNAME_ASKED:
                $username = $message->getText();
                if (preg_match(Registration::REGEX_USERNAME, $username)){
                    $chat->username = $username;
                    $fsm->apply(Registration::TRANSITION_ASK_KEEP_ENTERED_USERNAME);
                    $response = new Message('command.register.keep_entered_username', ['%username%' => $chat->username]);
                } else {
                    $response = new Message('command.register.regex');
                }
                break;

            case Registration::STATUS_KEEP_ENTERED_USERNAME_ASKED:
                switch($message->getText()) {
                    case $lang->trans('general.yes'):
                        $chat->username;
                        $fsm->apply(Registration::TRANSITION_KEEPING_ENTERED_USERNAME);
                        $response = new Message('command.register.success', ['%username%' => $chat->username]);
                        break;
                    case $lang->trans('general.no'):
                        $fsm->apply(Registration::TRANSITION_NOT_KEEPING_ENTERED_USERNAME);
                        $response = new Message("command.register.transition_ask_name");
                        break;
                    default:
                        $response = new Message('general.yesOrNoPls');
                        break;
                }
                break;
            case Registration::STATUS_REGISTERED:
                $response = new Message('command.register.alreadyRegistered');
            break;
            default:
                $response = new Message('command.register.cantRegister');
                break;

        }
        $chat->save();
        return $response;


    }


    protected function runGroup(GroupChat $chat, TelegramMessage $message)
    {
        return new Message('command.register.group');
    }
}