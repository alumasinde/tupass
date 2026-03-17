<?php

namespace App\Core;

class Controller
{
    protected function view($view, $data = [], $layout = 'app')
    {
        View::render($view, $data, $layout);
    }

    protected function redirect($url)
    {
        Response::redirect($url);
    }

    protected function json($data)
    {
        Response::json($data);
    }
}
