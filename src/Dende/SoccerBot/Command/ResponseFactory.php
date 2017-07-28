<?php
/**
 * Created by PhpStorm.
 * User: c
 * Date: 25.07.17
 * Time: 17:06
 */

namespace Dende\SoccerBot\Command;


use Dende\SoccerBot\Model\Chat;
use Dende\SoccerBot\FiniteStateMachine\BetFSM;
use Dende\SoccerBot\FiniteStateMachine\RegistrationFSM;
use Dende\SoccerBot\Repository\MatchRepository;
use Dende\SoccerBot\Repository\TeamRepository;
use Dende\SoccerBot\Telegram\Response;

class ResponseFactory
{
    /** @var CommandFactory */
    private $commandFactory;
    /** @var MatchRepository */
    private $matchRepo;
    /** @var TeamRepository */
    private $teamRepo;

    public function __construct(CommandFactory $commandFactory, MatchRepository $matchRepo, TeamRepository $teamRepo)
    {
        $this->commandFactory  = $commandFactory;
        $this->matchRepo       = $matchRepo;
        $this->teamRepo        = $teamRepo;
    }

    public function createResponse(Chat $chat, CommandInterface $command = null): Response{

        $message = $chat->getCurrentUpdate()->getMessage();


        if (is_null($command)) {
            //user is in registration process
            if ($chat->registerstatus !== RegistrationFSM::STATUS_UNREGISTERED &&
                $chat->registerstatus !== RegistrationFSM::STATUS_REGISTERED){
                $command = $this->commandFactory->createFromString("register");
            }
            if ($chat->betstatus === BetFSM::STATUS_GOALS_ASKED){
                $command = $this->commandFactory->createFromString("bet");
            }
        }

        // we have a command, give it its necessary repos
        switch (true){
            case $command instanceof RegisterCommand:

                break;
            case $command instanceof BetCommand:
                $command->setMatchRepo($this->matchRepo);
                $command->setTeamRepo($this->teamRepo);
                break;
            case is_null($command);
                return new Response();

        }

        $response = $command->run($chat, $message);
        return $response;
    }
}