<?php

namespace app\core;

class RouterCore{

    private $uri;
    private $method;
    private $getArr = [];

    private $postArr = [];

    private $deleteArr = [];

    public function __construct(){
       $this->initialize();
       $this->setRoute();
       $this->execute();

    }

    private function setRoute(){

        if( strpos( $_SERVER['SERVER_SOFTWARE'], 'Apache') !== false){
            require_once('../app/config/Router.php');
       }else{
            require_once($_SERVER['DOCUMENT_ROOT'].'/../app/config/Router.php');    
       }
    }
    private function initialize(){

        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
        //$_SESSION['nome'] = "Bruno";

        //tratamento do URI, conflito com Query string
        if (strpos($this->uri, '?'))
            $this->uri = mb_substr($this->uri, 0, strpos($this->uri, '?'));

        $ex = explode('/',$this->uri);
        
        $uri = $this->normalizeURI($ex);
        
        for($i = 0; $i< UNSET_URI_COUNT; $i++){
            unset($uri[$i]);
        }
        $uri = $this->normalizeURI($uri);
        //$this->uri = implode('/', $this->normalizeURI($uri));
       // $uri = $this->uri;
        if (DEBUG_URI)
            dd($uri);
        
    }

    private function execute(){
        switch($this->method){
            case 'GET':
                $this->executeGet();
                break;
            case 'POST':
                $this->executePOST();
                break;
            case 'DELETE':
                $this->executeDELETE();
                break;
        }
    }

    private function executeGet(){
        $error_ = true;
        foreach($this->getArr as $get){
            if ($this->matchRoute($get['router'], $this->uri, $params)){
                $error_ = false;
                if (is_callable($get['call'])){
                    $get['call']($params);
                } else {
                    $this->executeController($get['call'], $params);
                }
                break;
            }
        }
        if ($error_){
            (new \app\controller\MessageController)->message('404','Essa pagina não existe!');
        }
    }

    private function executePOST(){
        $error_ = true;
        foreach($this->postArr as $post){
            if ($this->matchRoute($post['router'], $this->uri, $params)){
                $error_ = false;
                if (is_callable($post['call'])){
                    $post['call']($params);
                } else {
                    $this->executeController($post['call'], $params);
                }
                break;
            }
        }
        if ($error_){
            (new \app\controller\MessageController)->message('404','Essa pagina não existe!');
        }
    }

    private function executeDELETE(){
        $error_ = true;
        foreach($this->deleteArr as $delete){
            if ($this->matchRoute($delete['router'], $this->uri, $params)){
                $error_ = false;
                if (is_callable($delete['call'])){
                    $delete['call']($params);
                } else {
                    $this->executeController($delete['call'], $params);
                }
                break;
            }
        }
        if ($error_){
            (new \app\controller\MessageController)->message('404','Essa pagina não existe!');
        }
    }

    private function executeController($route, $params){
        $ex = explode("@", $route);
        if (!isset($ex[0]) || !isset($ex[1])){
            (new \app\controller\MessageController)->message('404','Essa Controller, ou método não existe.');
            return;
        }

        $const = 'app\\controller\\'.$ex[0];
        if (!class_exists($const)){
            (new \app\controller\MessageController)->message('404','controller não existe.');
            return;
        }
        if (!method_exists($const, $ex[1])){
            (new \app\controller\MessageController)->message('404','método não existe.');
            return;
        }

        call_user_func_array([new $const, $ex[1]], $params);
    }

    private function matchRoute($route, $uri, &$params){
        $routeParts = explode('/', $route);
        $uriParts = explode('/', $uri);

        if (count($routeParts) !== count($uriParts)){
            return false;
        }

        $params = [];
        for ($i = 0; $i < count($routeParts); $i++){
            if (preg_match('/^\{.*\}$/', $routeParts[$i])){
                $params[] = $uriParts[$i];
            } else if ($routeParts[$i] !== $uriParts[$i]){
                return false;
            }
        }

        return true;
    }


    private function get($router,$call){
        $this->getArr [] = [
            'router' => $router,
            'call' => $call
        ];

    }

    private function post($router,$call){
        $this->postArr [] = [
            'router' => $router,
            'call' => $call
        ];
    }

    private function delete($router,$call){
        $this->deleteArr [] = [
            'router' => $router,
            'call' => $call
        ];
    }

    private function normalizeURI($arr){
        return array_values( array_filter($arr) );

    }
}