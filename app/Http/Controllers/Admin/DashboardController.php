<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;

class DashboardController extends AdminController
{
    //__construct
    function __construct(){
        parent::__construct();

    }

    // Index
    function index(Request $req){

        return view(VIEW_PATH.'dashboard.index');
    }
}
