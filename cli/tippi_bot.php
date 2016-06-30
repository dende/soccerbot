#!/usr/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../res/conf/propel/config.php';
require_once __DIR__ . '/../res/conf/api_config.php';

$soccerBot = new \Dende\SoccerBot\SoccerBot();
$soccerBot->init();
$soccerBot->run();
