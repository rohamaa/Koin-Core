<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/TestCase.php';
require __DIR__ . '/TestGateway.php';
require __DIR__ . '/PlainTestGateway.php';

Rohama\Translator\Translator::register('Koin', __DIR__ . '/lang');
Rohama\Translator\Translator::register('Koin-test', __DIR__ . '/lang');
