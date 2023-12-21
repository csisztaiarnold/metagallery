<?php
declare(strict_types=1);

// Create a very simple .env file if it doesn't exist.
if(!file_exists(__DIR__  . '/.env')) {
    $content = 'MAIN_LOGO="Logo"
MAIN_DESCRIPTION="Description"
EDITOR_PASSWORD="mypass"';
    $fp = fopen(__DIR__ . "/.env","wb");
    fwrite($fp,$content);
    fclose($fp);
}

include(__DIR__ . "/vendor/autoload.php");

// dd helper method
include(__DIR__ . "/vendor/larapack/dd/src/helper.php");

// Routes
include(__DIR__ . "/routes.php");



