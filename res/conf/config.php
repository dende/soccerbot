<?php
use Carbon\Carbon;

setlocale(LC_TIME, 'German');
Carbon::setLocale("de");
return [
	'log' =>  __DIR__ . '/../../var/log/em2016tippbot.log',
	'FSM_CHAT' => [
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
