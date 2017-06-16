<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

/**
 * Created by PhpStorm.
 * User: zxp86021
 * Date: 2017/6/16
 * Time: 上午 12:15
 */
$method =  $_SERVER['REQUEST_METHOD'];

$uri = str_replace('/index.php', '', $_SERVER['REQUEST_URI']);

$route = explode('/',$uri);

$input = json_decode(file_get_contents('php://input'), TRUE);

require_once ('Exceptions/Handler.php');

$exception_handler = new Handler();

if ( empty($_SESSION['username']) ) {
    if ( ($route[1] != 'login' || $route[1] != 'authors') && $method != 'POST' ) {
        $exception_handler->Unauthorized();

        exit;
    }
}

if (!file_exists(__DIR__ . '/Storage/authors.json')) {
    $fp = fopen(__DIR__ . '/Storage/authors.json', 'w');

    fwrite($fp, json_encode([], JSON_UNESCAPED_UNICODE));

    fclose($fp);
}

if (!file_exists(__DIR__ . '/Storage/posts.json')) {
    $fp = fopen(__DIR__ . '/Storage/posts.json', 'w');

    fwrite($fp, json_encode([], JSON_UNESCAPED_UNICODE));

    fclose($fp);
}

if ($route[1] == 'login') {
    require_once ('Controllers/AuthController.php');

    $controller = new AuthController();

    if ($method == 'POST') {
        $controller->postLogin($input);

        exit;
    } else {
        $exception_handler->MethodNotAllowed();

        exit;
    }
} else if ($route[1] == 'authors') {
    require_once ('Controllers/AuthorController.php');

    $controller = new AuthorController();

    if ($method == 'GET') {
        if (empty($route[2])) {
            $controller->index();

            exit;
        }

        $controller->show($route[2]);

        exit;
    } else if ($method == 'POST') {
        if (!empty($route[2])) {
            $exception_handler->MethodNotAllowed();

            exit;
        }

        $controller->store($input);

        exit;
    } else if ($method == 'PATCH') {
        if (empty($route[2])) {
            $exception_handler->MethodNotAllowed();

            exit;
        }

        $controller->update($route[2], $input);

        exit;
    } else {
        $exception_handler->MethodNotAllowed();

        exit;
    }
} else if ($route[1] == 'posts') {
    require_once ('Controllers/PostController.php');

    $controller = new PostController();

    if ($method == 'GET') {
        if (empty($route[2])) {
            $controller->index();

            exit;
        }

        $controller->show($route[2]);

        exit;
    } else if ($method == 'POST') {
        if (!empty($route[2])) {
            $exception_handler->MethodNotAllowed();

            exit;
        }

        $controller->store($input);

        exit;
    } else if ($method == 'PATCH') {
        if (empty($route[2])) {
            $exception_handler->MethodNotAllowed();

            exit;
        }

        $controller->update($route[2], $input);

        exit;
    } else if ($method == 'DELETE') {
        if (empty($route[2])) {
            $exception_handler->MethodNotAllowed();

            exit;
        }

        $controller->destroy($route[2]);

        exit;
    } else {
        $exception_handler->MethodNotAllowed();

        exit;
    }
} else {
    $exception_handler->MethodNotAllowed();

    exit;
}
