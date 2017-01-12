<?php

$loader = require( __DIR__ . '/vendor/autoload.php' );
$loader->addPsr4( 'ratecb\\', __DIR__ . '/src/' );

use ratecb\Controller;

(new Controller())->run();

