<?php
/**
 * Created by PhpStorm.
 * User: Christian Hartlage
 * Date: 29.06.2016
 * Time: 11:55
 */

namespace Dende\SoccerBot\Model;

use Finite\StateMachine\StateMachine;


interface ChatInterface
{

    public function getChatId();

    public function init();
    
    public function restore();

}