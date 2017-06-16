<?php

/**
 * Created by PhpStorm.
 * User: zxp86021
 * Date: 2017/6/16
 * Time: 下午 6:30
 */
class AuthController
{
    public function postLogin($input) {
        $authors = json_decode(file_get_contents(__DIR__ . '/../Storage/authors.json'), TRUE);

        $login = false;

        if (is_null($authors)) {
            $authors = [];
        }

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
    }
}