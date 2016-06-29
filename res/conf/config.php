<?php
use Carbon\Carbon;

\Kint::$maxLevels = 0;
setlocale(LC_TIME, 'German');
Carbon::setLocale("de");
return [
	'log' => [
		'streamhandlers' => [
			__DIR__ . '/../../var/log/em2016tippbot.log',
			'php://stdout'
		]
	],
	'FSM_PRIVATECHAT' => [
		'class'  => 'PrivateChat',
		'states' => [
			'liveticker' => [
				'type' => 'normal',
				'properties' => []
			],
			'muted' => [
				'type' => 'initial',
				'properties' => []
			]
		],
		'transitions' => [
			'live' => [
				'from' => ['muted'],
				'to'   => 'liveticker',
				'properties' => ['chat' => null, 'args' => null]
			],
			'mute' => [
				'from' => ['liveticker'],
				'to'   => 'muted',
				'properties' => ['chat' => null, 'args' => null]
			],
		]
	],
	'FSM_GROUPCHAT' => [
		'class'  => 'GroupChat',
		'states' => [
			'liveticker' => [
				'type' => 'normal',
				'properties' => []
			],
			'muted' => [
				'type' => 'initial',
				'properties' => []
			]
		],
		'transitions' => [
			'live' => [
				'from' => ['muted'],
				'to'   => 'liveticker',
				'properties' => ['chat' => null, 'args' => null]
			],
			'mute' => [
				'from' => ['liveticker'],
				'to'   => 'muted',
				'properties' => ['chat' => null, 'args' => null]
			],
		]
	]
];
