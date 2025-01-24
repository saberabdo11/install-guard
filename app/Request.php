<?php

namespace App;


class Request {


    private $queryParams = [];
    private $postData = [];
    private $jsonBody = [];


    public function __construct()
    {
        $this->queryParams = $_GET;
        $this->postData = $_POST;
        $input = file_get_contents('php://input');
        $this->jsonBody = json_decode($input,  true) ?? [];
    }


    public function all() {
        return array_merge($this->queryParams,  $this->postData, $this->jsonBody);
    }


    public function get($key, $default = null) {
        return $this->all()[$key] ?? $default;
    }

    public function only(array $keys) {
        return array_intersect_key($this->all(), array_flip($keys));
    }


    public function except(array $keys) {
        return array_diff($this->all(), array_flip($keys));
    }


    public function has($key) {
        return array_key_exists($key, $this->all());
    }

    public function validate(array $rules) {
        $data = $this->all();
        $errors = [];

        foreach($rules as $field => $rule) {
            if($rule === 'required' && empty($data[$field])) {
                $errors[$field] = "$field is required";
            }
        }

        if(!empty($errors)) {
            http_response_code(422);
            echo json_encode([
                'errors' => $errors
            ]);
        }

        return $data;

    }


}