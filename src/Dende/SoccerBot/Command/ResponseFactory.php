<?php
/**
 * Created by PhpStorm.
 * User: c
 * Date: 25.07.17
 * Time: 17:06
 */

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Chat;
use Dende\SoccerBot\Model\FiniteStateMachine\BetFSM;
use Dende\SoccerBot\Model\FiniteStateMachine\RegistrationFSM;
use Dende\SoccerBot\Model\Telegram\Response;

class ResponseFactory
{
    private $commandFactory;

    public function __construct(CommandFactory $commandFactory)
    {
        $this->commandFactory = $commandFactory;
    }

    public function createResponse(Chat $chat, CommandInterface $command = null): Response{

        $message = $chat->getCurrentUpdate()->getMessage();

        $response = new Response();

        if (!is_null($command)) {
            $response = $command->run($chat, $message);
        } else {
            //user is in registration process
            if ($chat->registerstatus !== RegistrationFSM::STATUS_UNREGISTERED &&
                $chat->registerstatus !== RegistrationFSM::STATUS_REGISTERED){
                $command = $this->commandFactory->createFromString("register");
                /** @var $command RegisterCommand */
                $response = $command->run($chat, $message);
            }
            if ($chat->betstatus === BetFSM::STATUS_WAITING_FOR_BET){
                $command = $this->commandFactory->createFromString("bet");
                /** @var BetCommand $command */
                $response =  $command->run($chat, $message);
            }
        }

        return $response;
    }
}