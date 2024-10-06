<?php

namespace App\Http\Controllers\Internal_chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Internal_chat\ChatController;

use App\Models\Staff;

class DashboardController extends Controller
{
    public function index(Request $request){

        if (Auth::guard('staff')->check()) {
            $staff = Auth::guard('staff')->user();
        } else {
            dd('User is not logged in');
        }

        $active_staff = Staff::where('online_status', '1')
            ->where('id', '!=', $staff->id)
            ->get();

        $inactive_staff = Staff::where('online_status', '0')
            ->where('id', '!=', $staff->id)
            ->get();

        $contact_list = [
            'active_contact' => $active_staff,
            'inactive_contact' => $inactive_staff,
        ];
        
        return view('Internal_chat.index', [
            'staffId' => $staff->id,
            'staff' => $staff,
            // 'contact_list' => $contact_list,
        ]);
    }
}
