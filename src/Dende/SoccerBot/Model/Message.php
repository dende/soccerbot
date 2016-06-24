<?php
namespace Dende\SoccerBot;

class Message
{
	protected $text;

	public function addLine($text)
	{
		if (is_null($text)){
			$this->text = $text . "\n";;
		} else {
			$this->text .= $text . "\n";
		}
	}
}