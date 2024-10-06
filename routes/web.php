<?php

use Illuminate\Support\Facades\Route;


Route::get('/hash-password', [App\Http\Controllers\Internal_chat\Auth\GeneratePasswordController::class, 'hashPassword']);

Route::get('/staff-login', [App\Http\Controllers\Internal_chat\Auth\LoginController::class, 'staffLoginPage'])->name('staff-login');
Route::post('/staff-login', [App\Http\Controllers\Internal_chat\Auth\LoginController::class, 'staffLogin']);


Route::middleware(['auth:staff'])->group(function () {

    Route::post('/staff-logout', [App\Http\Controllers\Internal_chat\Auth\LoginController::class, 'staffLogout'])->name('logout');

    Route::get('/welcome', [App\Http\Controllers\Internal_chat\DashboardController::class, 'index'])->name('welcome');

    Route::get('/get-active-contacts', [App\Http\Controllers\Internal_chat\ChatController::class, 'getActiveContacts'])->name('get-active-contacts');
    Route::get('/get-inactive-contacts', [App\Http\Controllers\Internal_chat\ChatController::class, 'getInactiveContacts'])->name('get-inactive-contacts');
    
    // Route::get('/view-single-contact/{staff_id}', [App\Http\Controllers\Internal_chat\ChatController::class, 'viewContacts'])->name('view-contact');

    Route::get('/load-chat-data/{staff_id}', [App\Http\Controllers\Internal_chat\ChatController::class, 'viewAndLoadChatData']);

    Route::get('/update-msg-seen/{staff_id}', [App\Http\Controllers\Internal_chat\ChatController::class, 'updateMessageStatus']);

    Route::post('/send-chat', [App\Http\Controllers\Internal_chat\ChatController::class, 'sendChat']);
    
});


Route::get('/run-all-commands', function () {
    $output = new \Symfony\Component\Console\Output\BufferedOutput();

    // Clear Cache
    Artisan::call('optimize:clear', [], $output);
    // Create Storage Link
    Artisan::call('storage:link', [], $output);

    Artisan::call('optimize', [], $output);
    // Route Cache
    Artisan::call('route:cache', [], $output);
    // Clear Route Cache
    Artisan::call('route:clear', [], $output);
    // Clear View Cache
    Artisan::call('view:clear', [], $output);
    // Clear Config Cache
    Artisan::call('config:cache', [], $output);
    // Clear Config Cache (again, if necessary)
    Artisan::call('config:clear', [], $output);

    echo '<pre>';
    return $output->fetch();
})->name('run-all-commands');

Route::get('/tab1', function () {
    return view('Internal_chat.index');
})->name('chat.tab1');

Route::get('/tab2', function () {
    return view('Internal_chat.index2');
})->name('chat.tab3');
