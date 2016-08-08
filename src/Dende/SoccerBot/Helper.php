<?php
namespace Dende\SoccerBot;

class Helper {
	public static function timeDifference(\DateTime $dt){
		$then = \Carbon\Carbon::instance($dt);
		$now = \Carbon\Carbon::now();
		$diffInDays = $now->diffInDays($then);
		$message = "";

		if ($diffInDays >= 1){
			//hours
			$message = "{$now->diffInDays($then)} Tagen";
		} else {
			//hours and minutes
			$nf = new \NumberFormatter("de-DE", \NumberFormatter::SPELLOUT);
			$diffInHours = $now->diffInHours($then);
			switch ($diffInHours){
				case 0:
					break;
				case 1:
					$message .= "einer Stunde";
					break;
				default:
					$message .= "{$nf->format($diffInHours)} Stunden";
					break;
			}

			$newDiffInMinutes = $now->diffInMinutes($then->copy()->subHours($diffInHours));

			switch ($newDiffInMinutes){
				case 0:

					break;
				case 1:
					if ($diffInHours > 0){
						$message .= " und einer Minute";
					} else {
						$message .= "einer	 Minute";
					}
					break;
				default:
					if ($diffInHours > 0){
						$message .= " und {$nf->format($newDiffInMinutes)} Minuten";
					} else {
						$message .= "{$nf->format($newDiffInMinutes)} Minuten";
					}
					break;
			}
		}
		return $message;
	}
}