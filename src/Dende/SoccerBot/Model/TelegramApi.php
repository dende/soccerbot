<?php


namespace Dende\SoccerBot\Model;


use Dende\SoccerBot\Command\CommandFactory;
use Dende\SoccerBot\Repository\ChatRepository;
use Symfony\Component\Translation\Translator;
use Telegram\Bot\Api;

class TelegramApi
{
    /** @var Api  */
    private $telegram;
    private $offset;
    /** @var CommandFactory */
    private $commandFactory;

    public function __construct(Translator $lang, CommandFactory $commandFactory)
    {
        $this->lang = $lang;
        $this->commandFactory = $commandFactory;
        $this->offset = 0;
        $this->telegram = new Api(TELEGRAM_BOT_TOKEN);
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


                $command = $this->commandFactory->createFromMessage($message);
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

    public function liveticker(Match $match, $info) {

        foreach ($fsms as $fsm){
            /** @var \Finite\StateMachine\StateMachine $fsm */
            if ($fsm->getCurrentState() == 'liveticker'){
                $message = new Message();

                $homeTeam = $match->getHomeTeam();
                $awayTeam = $match->getAwayTeam();

                /** @var GroupChat $chat */
                $chat = $fsm->getObject();

                if (array_get($info, 'status') == 'IN_PLAY'){
                    $message->addLine(
                        'live.matchStarted',
                        [
                            '%homeTeamName%'  => $homeTeam->getName(),
                            '%awayTeamName%'  => $awayTeam->getName(),
                        ]
                    );
                }

                if (array_has($info, 'homeTeamGoalsScored')){
                    $goalsScored         = array_get($info, 'homeTeamGoalsScored');
                    $teamScoredName      = $homeTeam->getName();
                    $teamConcededName    = $awayTeam->getName();
                } else if (array_has($info, 'awayTeamGoalsScored')){
                    $goalsScored         = array_get($info, 'awayTeamGoalsScored');
                    $teamScoredName      = $awayTeam->getName();
                    $teamConcededName    = $homeTeam->getName();
                }

                if (!empty($goalsScored)){
                    /** @noinspection PhpUndefinedVariableInspection */
                    $message->addLine(
                        'live.teamScored',
                        [
                            '%teamScoredName%'   => $teamScoredName,
                            '%teamConcededName%' => $teamConcededName,
                            '%goals%'            => $goalsScored
                        ],
                        $goalsScored
                    );
                    $message->addLine(
                        'live.newScore',
                        [
                            '%homeTeamGoals%' => $match->getHomeTeamGoals(),
                            '%awayTeamGoals%' => $match->getAwayTeamGoals(),
                        ]
                    );
                }

                if (array_get($info, 'status') == 'FINISHED'){
                    $message->addLine(
                        'live.finished',
                        [
                            '%homeTeamName%' => $homeTeam->getName(),
                            '%awayTeamName%' => $awayTeam->getName()
                        ]
                    );
                    $message->addLine(
                        'live.finalScore',
                        [
                            '%homeTeamGoals%' => $match->getHomeTeamGoals(),
                            '%awayTeamGoals%' => $match->getAwayTeamGoals(),
                        ]
                    );
                }
                $this->sendMessage($message, $chat);
            }
        }
    }

}