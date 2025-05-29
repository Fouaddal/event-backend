<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;



class UserController extends Controller
{
    public function me(Request $request)
{
    return response()->json($request->user());
}

// In your controller, e.g. UserController.php
public function __construct()
{
    $this->middleware(['auth:sanctum', 'verified']);
}


}