<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Controller\GeneratePostController;
use App\Controller\IndexController;
use App\Controller\WebsocketController;
use Hyperf\HttpServer\Router\Router;

//Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@login');

Router::get('/login', [IndexController::class, 'login']);
Router::get('/chat', WebsocketController::class);
Router::get('/generate-post', [GeneratePostController::class, 'index2']);

