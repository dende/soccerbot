<?php


namespace Dende\SoccerBot\Command;



use Dende\SoccerBot\Model\Chat;
use Dende\SoccerBot\FiniteStateMachine\RegistrationFSM;
use Dende\SoccerBot\Telegram\Response;
use Telegram\Bot\Objects\Message as TelegramMessage;

class RegisterCommand extends AbstractCommand
{

    //TODO: Check duplicate usernames
     function runPrivate(Chat $chat, TelegramMessage $message)
    {

        $username = $message->getChat()->getUsername();
        $lang = $chat->getLang();
        $fsm = $chat->getRegisterFsm();
        $currentRegisterState = $fsm->getCurrentState()->getName();

        switch($currentRegisterState){
            case RegistrationFSM::STATUS_UNREGISTERED:
                if (empty($username)) {
                    $response = new Response("command.register.transition_ask_name");
                    $fsm->apply(RegistrationFSM::TRANSITION_ASK_ENTER_USERNAME);
                } else {
                    $keyboard = [
                        [$lang->trans('general.yes'), $lang->trans('general.no')],
                    ];
                    $response = new Response($lang->trans('command.register.keepUsername', ['%username%' => $username]), $keyboard);
                    $fsm->apply(RegistrationFSM::TRANSITION_ASK_KEEP_TELEGRAM_USERNAME);
                }
                break;
            case RegistrationFSM::STATUS_KEEP_TELEGRAM_USERNAME_ASKED:
                switch($message->getText()) {
                    case $lang->trans('general.yes'):
                        $chat->username = $message->getChat()->getUsername();
                        $fsm->apply(RegistrationFSM::TRANSISTION_USING_TELEGRAM_USERNAME);
                        $response = new Response($lang->trans('command.register.success', ['%username%' => $chat->username]));
                        break;
                    case $lang->trans('general.no'):
                        $fsm->apply(RegistrationFSM::TRANSISTION_NOT_USING_TELEGRAM_USERNAME);
                        $response = new Response($lang->trans("command.register.transition_ask_name"));
                        break;
                    default:
                        $response = new Response($lang->trans('general.yesOrNoPls'));
                        break;
                }
                break;
            case RegistrationFSM::STATUS_ENTER_USERNAME_ASKED:
                $username = $message->getText();
                if (preg_match(RegistrationFSM::REGEX_USERNAME, $username)){
                    $chat->username = $username;
                    $fsm->apply(RegistrationFSM::TRANSITION_ASK_KEEP_ENTERED_USERNAME);
                    $keyboard = [
                        [$lang->trans('general.yes'), $lang->trans('general.no')],
                    ];
                    $response = new Response($lang->trans('command.register.keep_entered_username', ['%username%' => $chat->username]), $keyboard);
                } else {
                    $response = new Response($lang->trans('command.register.regex'));
                }
                break;

            case RegistrationFSM::STATUS_KEEP_ENTERED_USERNAME_ASKED:
                switch($message->getText()) {
                    case $lang->trans('general.yes'):
                        $chat->username;
                        $fsm->apply(RegistrationFSM::TRANSITION_KEEPING_ENTERED_USERNAME);
                        $response = new Response($lang->trans('command.register.success', ['%username%' => $chat->username]));
                        break;
                    case $lang->trans('general.no'):
                        $fsm->apply(RegistrationFSM::TRANSITION_NOT_KEEPING_ENTERED_USERNAME);
                        $response = new Response($lang->trans("command.register.transition_ask_name"));
                        break;
                    default:
                        $response = new Response($lang->trans('general.yesOrNoPls'));
                        break;
                }
                break;
            case RegistrationFSM::STATUS_REGISTERED:
                $response = new Response($lang->trans('command.register.alreadyRegistered', ['%username%' => $chat->username]));
            break;
            default:
                $response = new Response($lang->trans('command.register.cantRegister'));
                break;

        }
        $chat->save();
        return $response;


    }


    function runGroup(Chat $chat, TelegramMessage $message)
    {
        return new Response($chat->getLang()->trans('command.register.group'));
    }

}