#!/usr/bin/env php
<?php

use \Illuminate\Database\Capsule\Manager as Capsule;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../res/conf/api_config.php';

$capsule = new Capsule();

$capsule->addConnection([
    'driver'    => 'sqlite',
    'database'  => __DIR__ . '/../res/db/soccerbot.sqlite',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$soccerBot = new \Dende\SoccerBot\SoccerBot();
$soccerBot->init();
$soccerBot->run();
