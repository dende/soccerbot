<?php

return [
	'live' => [
        'turnedOn'     => 'Liveticker eingeschaltet.',
        'turnedOff'    => 'Ich sage jetzt gar nix mehr.',
		'matchStarted' => 'Das Spiel *%homeTeamName%* %homeTeamEmoji% gegen %awayTeamEmoji% *%awayTeamName%* hat begonnen.',
		'teamScored'   => '*%teamScoredName%* hat ein Tor gegen *%teamConcededName%* erzielt.|*%teamScoredName%* hat %goals% Tore gegen *%teamConcededName%* erzielt.',
        'newScore'     => 'Es steht nun %homeTeamEmoji% *%homeTeamGoals% - %awayTeamGoals%* %awayTeamEmoji%.',
        'finalScore'   => 'Das Spiel *%homeTeamName%* %homeTeamEmoji% gegen %awayTeamEmoji% *%awayTeamName%*  ist vorbei. Der Endstand lautet %homeTeamEmoji% *%homeTeamGoals% - %awayTeamGoals%* %awayTeamEmoji%'
    ],
    'command' => [
        'info' => 'Aktueller Status ist %status%.',
        'curr' =>[
            'currentMatches'   => 'Momentan läuft folgendes Spiel:|Momentan laufen folgende Spiele:',
            'noCurrentMatches' => 'Momentan läuft kein Spiel',
            'currentMatch'     => '*%homeTeamName%* %homeTeamEmoji% gegen %awayTeamEmoji% *%awayTeamName%*, es steht *%homeTeamGoals% - %awayTeamGoals%*'
        ],
        'next' => [
            'nextMatch' => '*%homeTeamName%* %homeTeamEmoji% gegen %awayTeamEmoji% *%awayTeamName%* (beginnt in %difference%).',
            'nextMatches' => 'Das nächste Spiel ist: |Die nächsten Spiele sind: ',
        ]
    ]
];