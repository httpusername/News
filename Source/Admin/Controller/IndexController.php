<?php

namespace Admin\Controller;

use Common\Controller;

if(!SITE) {exit('Access Denied');}
class IndexController extends Controller
{
    static public function index()
    {
        echo 'index...';
    }

}