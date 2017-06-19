<?php

/**
 * Created by PhpStorm.
 * User: zxp86021
 * Date: 2017/6/16
 * Time: 下午 7:46
 */
class PostController
{
    function __construct() {
        if ( is_null(json_decode(file_get_contents(__DIR__ . '/../Storage/authors.json'), TRUE)) ) {
            $this->authors = [];
        } else {
            $this->authors = json_decode(file_get_contents(__DIR__ . '/../Storage/authors.json'), TRUE);
        }

        if ( is_null(json_decode(file_get_contents(__DIR__ . '/../Storage/posts.json'), TRUE)) ) {
            $this->posts = [];
        } else {
            $this->posts = json_decode(file_get_contents(__DIR__ . '/../Storage/posts.json'), TRUE);
        }
    }

    public function index() {
        echo json_encode($this->posts, JSON_UNESCAPED_UNICODE);
    }

    public function show($id) {
        $get = false;

        foreach ($this->posts as $post) {
            if ($post['id'] == $id) {
                $get = true;

                echo json_encode($post, JSON_UNESCAPED_UNICODE);

                exit;
            }

        }

        if (!$get) {
            http_response_code(404);

            echo json_encode(['message' => '沒有這則文章'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function store($input) {
        foreach ($this->authors as $author) {
            if ($author['username'] == $_SESSION['username']) {

                $author_data = $author;

                break;
            }
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
        } while (in_array($pid, $this->posts) || empty($pid));

        $now = new DateTime('now');
        
        $posts = $this->posts;

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

        $fp = fopen(__DIR__ . '/../Storage/posts.json', 'w');

        fwrite($fp, json_encode($posts, JSON_UNESCAPED_UNICODE));

        fclose($fp);

        http_response_code(201);

        echo json_encode($insert, JSON_UNESCAPED_UNICODE);
    }

    public function update($id, $input) {
        $patch = false;

        $tmp = [];

        foreach ($this->posts as $post) {
            if ($post['id'] == $id) {
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

        $fp = fopen(__DIR__ . '/../Storage/posts.json', 'w');

        fwrite($fp, json_encode($tmp, JSON_UNESCAPED_UNICODE));

        fclose($fp);

        if ($patch) {
            echo json_encode($edited, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);

            echo json_encode(['message' => '沒有這則文章'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function destroy($id) {
        $tmp = [];

        $deleted_flag = false;

        foreach ($this->posts as $post) {
            if ($post['id'] != $id) {
                array_push($tmp, $post);
            } else {
                $deleted = $post;

                $deleted_flag = true;
            }
        }

        if ($deleted_flag) {
            $fp = fopen(__DIR__ . '/../Storage/posts.json', 'w');

            fwrite($fp, json_encode($tmp, JSON_UNESCAPED_UNICODE));

            fclose($fp);

            $remain = count($tmp); // 剩下幾篇文章

            echo json_encode(['remain' => $remain], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);

            echo json_encode(['message' => '沒有這則文章'], JSON_UNESCAPED_UNICODE);
        }
    }
}
