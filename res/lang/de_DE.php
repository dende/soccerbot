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
            'cantTurnOn'   => 'Kann den Liveticker nicht ausschalten',
        ],
        'noop' => 'Unbekannter Befehl "%command%"',
        'info' => [
            'muted' => 'Aktueller Status ist gemuted.',
            'liveticker' => 'Aktueller Status ist Liveticker.',
        ],
        'curr' =>[
            'currentMatches'   => 'Momentan läuft folgendes Spiel:|Momentan laufen folgende Spiele:',
            'noCurrentMatches' => 'Momentan läuft kein Spiel',
            'currentMatch'     => '*%homeTeamName%* gegen *%awayTeamName*, es steht *%homeTeamGoals% - %awayTeamGoals*'
        ],
        'next' => [
            'nextMatch' => '*%homeTeamName%* gegen *%awayTeamName%* (beginnt in %difference%).',
            'nextMatches' => '{1} Das nächste Spiel ist: |[2,Inf[ Die nächsten Spiele sind: ',
        ],
        'register' => [
            'group' => 'Registrierungen bitte im privaten Chat mit dem Bot durchführen',
            'keepUsername' => 'Dein Benutzername scheint *%username%* zu sein. Möchtest du diesen Benutzernamen auch für die Wettgruppe benutzen?',
            'cantRegister' => 'Registrierung nicht möglich',
            'alreadyRegistered' => 'Du bist bereits registriert, dein Nutzername lautet *%username%*.',
            'transition_ask_name' => 'Bitte gib deinen gewünschten Nutzernamen an:',
            'keep_entered_username' => 'Möchtest du den Nutzernamen *%username%* registrieren?',
            'success' => 'Registrierung erfolgreich. Du bist jetzt mit dem Namen *%username%* beim Tippspiel angemeldet.',
            'regex' => 'Der Nutzername darf nur alphanumerische Zeichen enthalten und muss 3-18 Zeichen lang sein.'
        ],
        'bet' => [
            'register'  => 'Bitte zuerst registrieren. Dazu /register benutzen.',
            'noMatches' => 'Es gibt keine Spiele zu denen du tippen kannst.',
            'yourBet'   => 'Das Spiel %homeTeamName% gegen %awayTeamName% findet am %date% statt. Welches Ergebnis tippst du?',
            'group'     => 'Tipps bitte im privaten Chat abgeben',
            'regex'     => "Tipps bitte im Format XX:YY abgeben.\n(z.B. 3:2 oder 11:0), oder STOP schreiben",
            'done'      => 'Es gibt keine weiteren Spiele für die du Tipps abgeben kannst',
            'info'      => 'Dein Tipp für das Spiel %homeTeamName% gegen %awayTeamName% lautet: *%bet%*',
            'stopped'   => 'Du hast die Tippeingabe unterbrochen. Es gibt noch *%num%* spiele auf die du tippen kannst.',
            'failed'    => 'Das Anlegen der Wette hat nicht funktioniert, probier es bitte nochmal oder schreib STOP'
        ],
        'betinfo' => [
            'nothing' => 'Zur Zeit gibt es keine Spiele, auf die getippt werden kann.',
            'noBets' => 'Du hast zur Zeit keine Tipps abgegeben.',
            'followingBets' => "Du hast für folgende Spiele Tipps abgegeben:\n",
            'bet' => '*%homeTeamName%* gegen *%awayTeamName%*: *%bet%*',
            'noOpen' => 'Es gibt zur Zeit keine Spiele für die du Tipps abgeben kannst.',
            'followingOpen' => "Du kannst für folgende Spiele noch Tipps abgeben:\n",
            'open' => '*%homeTeamName%* gegen *%awayTeamName%* am *%date%*',

        ]
    ],
    'general' => [
        'yes' => 'Ja',
        'no'  => 'Nein',
        'yesOrNoPls' => 'Bitte mit "Ja" oder "Nein" antworten.'
    ]
];