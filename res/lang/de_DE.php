<?php

return [
	'live' => [
		'matchStarted' => 'Das Spiel *%homeTeamName%* gegen *%awayTeamName%* hat begonnen.',
		'teamScored'   => '*%homeTeamName%* hat ein Tor gegen *%awayTeamName%* erzielt.|*%homeTeamName%* hat %goals% Tore gegen *%awayTeamName%* erzielt.',
        'newScore'     => 'Es steht nun *%homeTeamGoals% - %awayTeamGoals%.',
        'finished'     => 'Der Endstand lautet *%homeTeamGoals% - %awayTeamGoals%*'
    ],
    'command' => [
        'live' => [
            'turnedOn'     => 'Liveticker eingeschaltet.',
            'alreadyOn'    => 'Liveticker ist bereits eingeschaltet',
            'cantTurnOn'   => 'Kann den Liveticker nicht einschalten',
        ],
        'mute' => [
            'turnedOn'     => 'Liveticker ausgeschaltet.',
            'alreadyOn'    => 'Liveticker ist bereits ausgeschaltet',
            'cantTurnOn'   => 'Kann den Liveticker ausgeschaltet',
        ],
        'noop' => 'Unbekannter Befehl "%command%"',
        'info' => 'Aktueller Status ist %status%.',
        'curr' =>[
            'currentMatches'   => 'Momentan läuft folgendes Spiel:|Momentan laufen folgende Spiele:',
            'noCurrentMatches' => 'Momentan läuft kein Spiel',
            'currentMatch'     => '*%homeTeamName%* gegen *%awayTeamName*, es steht *%homeTeamGoals% - %awayTeamGoals*'
        ],
        'next' => [
            'nextMatch' => '*%homeTeamName%* gegen *%awayTeamName%* (beginnt in %difference%).',
            'nextMatches' => '{1} Das nächste Spiel ist: |[2,Inf[ Die nächsten Spiele sind: ',
        ]
    ]
];