<?php
namespace Dende\SoccerBot\Model\Telegram;

use Symfony\Component\Translation\Translator;

class Response
{

    private $lines;
    private $keyboard;

    function __construct($text = null, $keyboard = null)
    {
        $this->keyboard = $keyboard;
        $this->lines = [];
        if ($text != null){
            $this->addLine($text);
        }
    }

    public function addLine($text)
    {
        if ($text)
            $this->lines[] = $text;
    }

    public function setKeyboard($keyboard){
        $this->keyboard = $keyboard;
    }

    public function getKeyboard()
    {
        return $this->keyboard;
    }

    public function getText(): String {
        $text = null;
        foreach ($this->lines as $line) {
            $text .= $line . "\n";
        }
        return $text;
    }
}