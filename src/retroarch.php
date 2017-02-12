#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$test = new Test();

$application->add(new Command\DownloadCoresCommand());
$application->run();