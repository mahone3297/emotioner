<?php

class Index extends EmcController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        EmcGlobal::get_loader()->view('index.php', array(
            'msg' => 'Hello Emotioner!'
        ));
    }
}