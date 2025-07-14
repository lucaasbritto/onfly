<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(){
        return User::where('is_admin', false)->select('id', 'name')->get();
    }

    public function admins(){
        return User::where('is_admin', true)->select('id', 'name')->get();
    }
}
