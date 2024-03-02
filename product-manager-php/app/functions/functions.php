<?php

/**
 * @param  mixed $params
 * @param  bool $die true if you want to die
 * @return void
 */
function dd($params = [], $die = true)
{
    echo '<pre>';
    print_r($params);
    echo '</pre>';

    if ($die) die();
}

/**
 * @param  string $url URL to redirect
 * @return void
 */
function redirect(string $url)
{
    header('Location: ' . $url);
    exit;
}