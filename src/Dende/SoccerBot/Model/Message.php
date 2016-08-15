<?php
namespace Dende\SoccerBot\Model;

use Symfony\Component\Translation\Translator;

class Message
{

    private $lines;

    function __construct($text = null, $vars = [], $choice = false)
    {
        $this->lines = [];
        if ($text != null){
            $this->addLine($text, $vars, $choice);
        }
    }

    /**
     * @param Translator $lang
     * @return null|string
     */
    public function translate(Translator $lang){
        $text = null;
        foreach ($this->lines as $line){
            if ($text != null){
                $text .= "\n";
            }
            if ($line['choice'] !== false){
                $text .=  $lang->transChoice($line['text'], $line['choice'], $line['vars']);
            } else {
                $text .= $lang->trans($line['text'], $line['vars']);
            }
        }
        return $text;
    }

    public function addLine($text, $vars = [], $choice = false)
    {
        $this->lines[] = ['text' => $text, 'vars' => $vars, 'choice' => $choice];
    }

}