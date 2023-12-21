<?php
declare(strict_types=1);

use App\Controllers\MainController;
use App\Controllers\ImageController;
use MiladRahimi\PhpRouter\Router;

$router = Router::create();
$router->get('/', [MainController::class, 'index']);
$router->any('/imagelist/?{gallery?}/?{parameters?}', [MainController::class, 'imageList']);
$router->post('/edit_image_description', [ImageController::class, 'editImageComment']);
$router->post('/search', [MainController::class, 'searchImage']);

$router->dispatch();