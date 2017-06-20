<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');

if ( isset($_SERVER['HTTP_ORIGIN']) ) {
    header('Access-Control-Allow-Origin:'.$_SERVER['HTTP_ORIGIN']);
} else {
    header('Access-Control-Allow-Origin: *');
}

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

require_once(__DIR__ . '/../Exceptions/Handler.php');

$exception_handler = new Handler();

if ($method == 'OPTIONS') {
    $request_headers = allheaders();

    if ( isset($request_headers['Access-Control-Request-Headers']) ) {
        $ACRH = $request_headers['Access-Control-Request-Headers'];
        header('Access-Control-Allow-Headers: '. $ACRH);
    }

    header('Access-Control-Max-Age: 18600');

    exit;
}

if (!file_exists(__DIR__ . '/../Storage/authors.json')) {
    $fp = fopen(__DIR__ . '/../Storage/authors.json', 'w');

    fwrite($fp, json_encode([], JSON_UNESCAPED_UNICODE));

    fclose($fp);
}

if (!file_exists(__DIR__ . '/../Storage/posts.json')) {
    $fp = fopen(__DIR__ . '/../Storage/posts.json', 'w');

    fwrite($fp, json_encode([], JSON_UNESCAPED_UNICODE));

    fclose($fp);
}

if ($route[1] == 'login') {
    require_once(__DIR__ . '/../Controllers/AuthController.php');

    $controller = new AuthController();

    if ($method == 'POST') {
        $controller->Login($input);

        exit;
    } else if ($method == 'GET') {
        $controller->LoginStatus();

        exit;
    } else {
        $exception_handler->MethodNotAllowed();

        exit;
    }
} else if ($route[1] == 'logout') {
    require_once(__DIR__ . '/../Controllers/AuthController.php');

    $controller = new AuthController();

    if ($method == 'POST') {
        $controller->Logout();
        
        exit;
    } else {
        $exception_handler->MethodNotAllowed();

        exit;
    }
}else if ($route[1] == 'authors') {
    require_once(__DIR__ . '/../Controllers/AuthorController.php');

    $controller = new AuthorController();

    if ($method == 'GET') {
        if ( empty($_SESSION['username']) ) {
            $exception_handler->Unauthorized();

            exit;
        }

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
        if ( empty($_SESSION['username']) ) {
            $exception_handler->Unauthorized();

            exit;
        }

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
    require_once(__DIR__ . '/../Controllers/PostController.php');

    $controller = new PostController();

    if ($method == 'GET') {
        if (empty($route[2])) {
            $controller->index();

            exit;
        }

        $controller->show($route[2]);

        exit;
    } else if ($method == 'POST') {
        if ( empty($_SESSION['username']) ) {
            $exception_handler->Unauthorized();

            exit;
        }

        if (!empty($route[2])) {
            $exception_handler->MethodNotAllowed();

            exit;
        }

        $controller->store($input);

        exit;
    } else if ($method == 'PATCH') {
        if ( empty($_SESSION['username']) ) {
            $exception_handler->Unauthorized();

            exit;
        }

        if (empty($route[2])) {
            $exception_handler->MethodNotAllowed();

            exit;
        }

        $controller->update($route[2], $input);

        exit;
    } else if ($method == 'DELETE') {
        if ( empty($_SESSION['username']) ) {
            $exception_handler->Unauthorized();

            exit;
        }

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

function allheaders()
{
    if (!is_array($_SERVER)) {
        return array();
    }

    $headers = array();
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
}
