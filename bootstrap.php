<?php
declare(strict_types=1);

$settings = parse_ini_file(__DIR__ . '/.env');
ini_set('display_errors', $settings['DEVELOPMENT_ENV']);

// Create a very simple .env file if it doesn't exist.
if(!file_exists(__DIR__  . '/.env')) {
    $content = 'MAIN_LOGO="Logo"
MAIN_DESCRIPTION="Description"
EDITOR_PASSWORD="mypass"
EDITOR_PASSWORD="password"
ENVIRONMENT="development"';
    $fp = fopen(__DIR__ . "/.env","wb");
    fwrite($fp,$content);
    fclose($fp);
}

include(__DIR__ . "/vendor/autoload.php");

// dd helper method
include(__DIR__ . "/vendor/larapack/dd/src/helper.php");

// Routes
include(__DIR__ . "/routes.php");



