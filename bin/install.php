#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Kyojin\JWT\Scripts\Installer;
use Kyojin\JWT\Scripts\ProviderInstaller;

Installer::postInstall();
ProviderInstaller::postInstall();