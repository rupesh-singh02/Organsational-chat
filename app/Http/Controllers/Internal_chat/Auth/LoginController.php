<?php

namespace App\Http\Controllers\Internal_chat\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function staffLoginPage()
    {
        return view('Internal_chat.Auth.login');
    }

    public function staffLogin(Request $request): JsonResponse
    {
        try {

            $credentials = $request->only('email', 'password');

            $validation = Validator::make($credentials, [
                'email' => 'required|email|exists:staff,email',
                'password' => 'required',
            ]);

            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            $staff = Staff::where('email', $credentials['email'])->first();

            if (!$staff || !Hash::check($credentials['password'], $staff->password)) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
            }
           
            if (!Auth::guard('staff')->attempt($credentials)) {                
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
            }
            
            $token = $staff->createToken('staff-token')->plainTextToken;

            $staff->online_status = 1;  //1 for online
            $staff->save();

            $message = 'User logged in successfully';
            $status = true;

            return response()->json([
                'status' => $status,
                'message' => $message,
                'token' => $token,
                'redirect_url' => route('welcome'),
                'staff' => $staff,
            ], 200);

        } catch (ValidationException $e) {
            Log::error('Validation Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Validation Error: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('Error during login: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function staffLogout(Request $request)
    {
        try {

            $staff = $request->user();

            // Revoke all of the user's tokens
            $staff->tokens()->delete(); // Use tokens() method, not token()

            // Update the online status
            $staff->online_status = 0;  // 0 for offline
            $staff->save();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            $message = 'Staff user logged out successfully';
            $status = true;

            return redirect('/staff-login');

        } catch (\Exception $e) {
            Log::error('Error during logout: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    
}
