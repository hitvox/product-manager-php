<?php

namespace app\controller;

use app\core\Controller;

class PagesController extends Controller
{
    public function home()
    {
        session_start();
        
        $message = isset($_SESSION['message']) ? $_SESSION['message'] : null;

        if(isset($_SESSION['message'])) unset($_SESSION['message']);

        $this->load('pages/home/index', ['message' => $message]);
    }

    public function product($id)
    {
        session_start();
        
        $message = isset($_SESSION['message']) ? $_SESSION['message'] : null;

        if(isset($_SESSION['message'])) unset($_SESSION['message']);
        $this->load('pages/products/index', ['message' => $message, 'id' => $id]);
    }

    public function categories()
    {
        session_start();
        
        $message = isset($_SESSION['message']) ? $_SESSION['message'] : null;

        if(isset($_SESSION['message'])) unset($_SESSION['message']);

        $this->load('pages/categories/index', ['message' => $message]);
    }
}