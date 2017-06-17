<?php

/**
 * Created by PhpStorm.
 * User: zxp86021
 * Date: 2017/6/16
 * Time: 下午 7:15
 */
class AuthorController
{
    function __construct() {
        if ( is_null(json_decode(file_get_contents(__DIR__ . '/../Storage/authors.json'), TRUE)) ) {
            $this->authors = [];
        } else {
            $this->authors = json_decode(file_get_contents(__DIR__ . '/../Storage/authors.json'), TRUE);
        }
    }

    public function index() {
        echo json_encode($this->authors, JSON_UNESCAPED_UNICODE);
    }

    public function show($username) {
        $get = false;

        foreach ($this->authors as $author) {
            if ($author['username'] == $username && $author['username'] == $_SESSION['username']) {
                $get = true;

                echo json_encode($author, JSON_UNESCAPED_UNICODE);

                exit;
            }

        }

        if (!$get) {
            http_response_code(404);

            echo json_encode(['message' => '沒有這個使用者'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function store($input) {
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

        foreach ($this->authors as $author) {
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
        
        $authors = $this->authors;

        array_push($authors, $insert);

        $fp = fopen(__DIR__ . '/../Storage/authors.json', 'w');

        fwrite($fp, json_encode($authors, JSON_UNESCAPED_UNICODE));

        fclose($fp);

        http_response_code(201);

        echo json_encode($insert, JSON_UNESCAPED_UNICODE);
    }

    public function update($username, $input) {
        $patch = false;

        $tmp = [];

        foreach ($this->authors as $author) {
            if ($author['username'] == $username && $author['username'] == $_SESSION['username']) {
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

        $fp = fopen(__DIR__ . '/../Storage/authors.json', 'w');

        fwrite($fp, json_encode($tmp, JSON_UNESCAPED_UNICODE));

        fclose($fp);

        if ($patch) {
            echo json_encode($edited, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);

            echo json_encode(['message' => '沒有這個使用者'], JSON_UNESCAPED_UNICODE);
        }
    }
}
