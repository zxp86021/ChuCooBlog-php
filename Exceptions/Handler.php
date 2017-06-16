<?php

/**
 * Created by PhpStorm.
 * User: zxp86021
 * Date: 2017/6/16
 * Time: 下午 7:25
 */
class Handler
{
    public function MethodNotAllowed() {
        http_response_code(405);

        echo json_encode(['message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
    }

    public function Unauthorized() {
        http_response_code(401);

        echo json_encode(['message' => '請先登入'], JSON_UNESCAPED_UNICODE);
    }
}