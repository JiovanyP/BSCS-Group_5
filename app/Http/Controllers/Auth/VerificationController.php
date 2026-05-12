<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class VerificationController extends Controller
{
    public function show()
    {
        return view('auth.verify'); // Blade file: resources/views/auth/verify.blade.php
    }
}
