<?php

namespace App\Http\Controllers\Internal_chat\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GeneratePasswordController extends Controller
{
    public function hashPassword(Request $request)
    {
        // Validate the request
        $password = 123456;

        // Hash the password
        $hashedPassword = Hash::make($password);

        // Return the hashed password (for demonstration purposes)
        return response()->json([
            'Password' => $password,
            'Hashed_password' => $hashedPassword,
        ]);
    }
}
