<?php
session_start();

header('Content-Type: application/json');
/**
 * Created by PhpStorm.
 * User: zxp86021
 * Date: 2017/6/16
 * Time: 上午 12:15
 */

$method =  $_SERVER['REQUEST_METHOD'];

$uri = str_replace('/index.php', '', $_SERVER['REQUEST_URI']);

$route = explode('/',$uri);


if (empty($_SESSION['username']) && $route[1] != 'login') {
    http_response_code(401);
    echo json_encode(['message' => '請先登入'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($route[1] == 'login') {
    if ($method == 'POST') {
        $input = json_decode(file_get_contents('php://input'), TRUE);

        $authors = json_decode(file_get_contents('./authors.json'), TRUE);

        $login = false;

        foreach ($authors as $author) {
            if ($input['username'] == $author['username'] && $input['password'] == $author['password']) {
                $login = true;

                $_SESSION['username'] = $author['username'];

                echo json_encode($author, JSON_UNESCAPED_UNICODE);

                exit;
            }
        }

        if (!$login) {
            http_response_code(401);

            echo json_encode(['message' => '帳號或密碼錯誤'], JSON_UNESCAPED_UNICODE);
        }

        exit;
    } else {
        http_response_code(405);

        echo json_encode(['message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);

        exit;
    }
} else if ($route[1] == 'authors') {
    if ($method == 'GET') {
        $authors = json_decode(file_get_contents('./authors.json'), TRUE);

        if (empty($route[2])) {
            echo json_encode($authors, JSON_UNESCAPED_UNICODE);

            exit;
        }

        $get = false;

        foreach ($authors as $author) {
            if ($author['username'] == $route[2] && $author['username'] == $_SESSION['username']) {
                $get = true;

                echo json_encode($author, JSON_UNESCAPED_UNICODE);

                exit;
            }

        }

        if (!$get) {
            http_response_code(404);

            echo json_encode(['message' => '沒有這個使用者'], JSON_UNESCAPED_UNICODE);
        }

        exit;
    } else if ($method == 'POST') {
        if (!empty($route[2])) {
            http_response_code(405);

            echo json_encode(['message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $authors = json_decode(file_get_contents('./authors.json'), TRUE);

        if (is_null($authors)) {
            $authors = [];
        }

        if (empty($input['username'])) {
            http_response_code(400);

            echo json_encode(['message' => 'username必填'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        if (empty($input['password'])) {
            http_response_code(400);

            echo json_encode(['message' => 'password 必填'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        if (empty($input['name'])) {
            http_response_code(400);

            echo json_encode(['message' => 'name 必填'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        if (empty($input['gender'])) {
            http_response_code(400);

            echo json_encode(['message' => 'gender 必填'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        if (empty($input['address'])) {
            http_response_code(400);

            echo json_encode(['message' => 'address 必填'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        foreach ($authors as $author) {
            if ($input['username'] == $author['username']) {
                http_response_code(400);

                echo json_encode(['message' => 'username 已被使用'], JSON_UNESCAPED_UNICODE);

                exit;
            }
        }

        $insert = [
            'username' => $input['username'],
            'password' => $input['password'],
            'name' => $input['name'],
            'gender' => $input['gender'],
            'address' => $input['address'],
        ];

        array_push($authors, $insert);

        $fp = fopen('./authors.json', 'w');

        fwrite($fp, json_encode($authors, JSON_UNESCAPED_UNICODE));

        fclose($fp);

        http_response_code(201);

        echo json_encode($insert, JSON_UNESCAPED_UNICODE);

        exit;
    } else if ($method == 'PATCH') {
        if (empty($route[2])) {
            http_response_code(405);

            echo json_encode(['message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $authors = json_decode(file_get_contents('./authors.json'), TRUE);

        $patch = false;

        $tmp = [];

        foreach ($authors as $author) {
            if ($author['username'] == $route[2] && $author['username'] == $_SESSION['username']) {
                $input = json_decode(file_get_contents('php://input'), TRUE);

                if (!empty($input['password'])) {
                    $author['password'] = $input['password'];
                }

                if (!empty($input['name'])) {
                    $author['name'] = $input['name'];
                }

                if (!empty($input['gender'])) {
                    $author['gender'] = $input['gender'];
                }

                if (!empty($input['address'])) {
                    $author['address'] = $input['address'];
                }

                $patch = true;

                $edited = $author;
            }

            array_push($tmp, $author);
        }

        $fp = fopen('./authors.json', 'w');

        fwrite($fp, json_encode($tmp, JSON_UNESCAPED_UNICODE));

        fclose($fp);

        if ($patch) {
            echo json_encode($edited, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);

            echo json_encode(['message' => '沒有這個使用者'], JSON_UNESCAPED_UNICODE);
        }

        exit;
    } else {
        http_response_code(405);

        echo json_encode(['message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);

        exit;
    }
} else if ($route[1] == 'posts') {
    if ($method == 'GET') {
        //$authors = json_decode(file_get_contents('./authors.json'), TRUE);

        $posts = json_decode(file_get_contents('./posts.json'), TRUE);

        if (empty($route[2])) {
            if (is_null($posts)) {
                $posts = [];
            }

            echo json_encode($posts, JSON_UNESCAPED_UNICODE);

            exit;
        }

        $get = false;

        foreach ($posts as $post) {
            if ($post['id'] == $route[2]) {
                $get = true;

                echo json_encode($post, JSON_UNESCAPED_UNICODE);

                exit;
            }

        }

        if (!$get) {
            http_response_code(404);

            echo json_encode(['message' => '沒有這則文章'], JSON_UNESCAPED_UNICODE);
        }

        exit;
    } else if ($method == 'POST') {
        if (!empty($route[2])) {
            http_response_code(405);

            echo json_encode(['message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $input = json_decode(file_get_contents('php://input'), TRUE);

        $posts = json_decode(file_get_contents('./posts.json'), TRUE);

        $authors = json_decode(file_get_contents('./authors.json'), TRUE);

        foreach ($authors as $author) {
            if ($author['username'] == $_SESSION['username']) {

                $author_data = $author;

                break;
            }
        }

        if (is_null($posts)) {
            $posts = [];
        }

        if (empty($input['title'])) {
            http_response_code(400);

            echo json_encode(['message' => 'title 必填'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        if (empty($input['content'])) {
            http_response_code(400);

            echo json_encode(['message' => 'content 必填'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        if (!is_array($input['tags'])) {
            http_response_code(400);

            echo json_encode(['message' => 'tags 必須是 array'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        do {
            $pid = rand(10,100000);
        } while (in_array($pid, $posts) || empty($pid));

        $now = new DateTime('now');

        $insert = [
            'id' => $pid,
            'title' => $input['title'],
            'content' => $input['content'],
            'created_at' => $now->format(DateTime::ISO8601),
            'updated_at' => $now->format(DateTime::ISO8601),
            'author' => $author_data,
            'tags' => $input['tags']
        ];

        array_push($posts, $insert);

        $fp = fopen('./posts.json', 'w');

        fwrite($fp, json_encode($posts, JSON_UNESCAPED_UNICODE));

        fclose($fp);

        http_response_code(201);

        echo json_encode($insert, JSON_UNESCAPED_UNICODE);

        exit;
    } else if ($method == 'PATCH') {
        if (empty($route[2])) {
            http_response_code(405);

            echo json_encode(['message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $posts = json_decode(file_get_contents('./posts.json'), TRUE);

        $patch = false;

        $tmp = [];

        foreach ($posts as $post) {
            if ($post['id'] == $route[2]) {
                $input = json_decode(file_get_contents('php://input'), TRUE);

                $now = new DateTime('now');

                $post['updated_at'] = $now->format(DateTime::ISO8601);

                if (!empty($input['title'])) {
                    $post['title'] = $input['title'];
                }

                if (!empty($input['content'])) {
                    $post['content'] = $input['content'];
                }

                if (!empty($input['tags'])) {
                    if (is_array($input['tags'])) {
                        $post['tags'] = $input['tags'];
                    } else {
                        http_response_code(400);

                        echo json_encode(['message' => 'tags 必須是 array'], JSON_UNESCAPED_UNICODE);

                        exit;
                    }
                }

                $patch = true;

                $edited = $post;
            }

            array_push($tmp, $post);
        }

        $fp = fopen('./posts.json', 'w');

        fwrite($fp, json_encode($tmp, JSON_UNESCAPED_UNICODE));

        fclose($fp);

        if ($patch) {
            echo json_encode($edited, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);

            echo json_encode(['message' => '沒有這則文章'], JSON_UNESCAPED_UNICODE);
        }

        exit;
    } else if ($method == 'DELETE') {
        if (empty($route[2])) {
            http_response_code(405);

            echo json_encode(['message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $posts = json_decode(file_get_contents('./posts.json'), TRUE);

        $tmp = [];

        foreach ($posts as $post) {
            if ($post['id'] != $route[2]) {
                array_push($tmp, $post);
            } else {
                $deleted = $post;
            }
        }

        $fp = fopen('./posts.json', 'w');

        fwrite($fp, json_encode($tmp, JSON_UNESCAPED_UNICODE));

        fclose($fp);

        echo json_encode($deleted, JSON_UNESCAPED_UNICODE);

        exit;
    } else {
        http_response_code(405);

        echo json_encode(['message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);

        exit;
    }
} else {
    http_response_code(405);

    echo json_encode(['message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);

    exit;
}
