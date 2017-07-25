<?php

namespace Dende\SoccerBot\Model;

use Dende\SoccerBot\FiniteStateMachine\BetFSM;
use Dende\SoccerBot\FiniteStateMachine\RegistrationFSM;
use Finite\StateMachine\StateMachine as FiniteStateMachine;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Translation\Translator;
use Telegram\Bot\Objects\Update;

/**
 * Skeleton subclass for representing a row from the 'privatechats' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Chat extends Model implements ChatInterface
{
    const TYPE_GROUP = 'group';
    const TYPE_PRIVATE = 'private';

    /** @var  FiniteStateMachine */
    private $registerFsm;
    /** @var  FiniteStateMachine */
    private $betFsm;

    protected $currentUpdate;


    protected $table = 'chats';
    public $timestamps = false;

    /** @var  Translator */
    protected $lang;

    public function getLang(): Translator
    {
        return $this->lang;
    }

    public function setLang(Translator $lang)
    {
        $this->lang = $lang;
    }

    public function init()
    {
        if ($this->isPrivate()){
            $this->registerFsm = RegistrationFSM::create($this);
            $this->betFsm      = BetFSM::create($this);
        }

    }

    public function restore(Update $update){
        $this->setCurrentUpdate($update);
        $this->init();
        //TODO: logic for restoring should go here
    }

    public function getChatId()
    {
        return $this->chat_id;
    }


    public function isPrivate()
    {
        return $this->type == Chat::TYPE_PRIVATE;
    }

    public function getRegisterFsm()
    {
        return $this->registerFsm;
    }

    public function getBetFsm(){
        return $this->betFsm;
    }

    /**
     * @return mixed
     */
    public function getCurrentUpdate(): Update
    {
        return $this->currentUpdate;
    }

    /**
     * @param mixed $currentUpdate
     */
    public function setCurrentUpdate(Update $currentUpdate)
    {
        $this->currentUpdate = $currentUpdate;
    }

}
