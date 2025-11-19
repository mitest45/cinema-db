<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //__construct
    public function __construct(){
        define('VIEW_PATH','admin.');
    }
}
