<?php

use Illuminate\Support\Facades\Route;



Auth::routes();

Route::get('/contacts','ContactsController@get');
Route::get('/contacts', [App\Http\Controllers\ContactsController::class, 'get']);
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/conversation/{id}', [App\Http\Controllers\ContactsController::class, 'getMessagesFor']);
Route::post('/conversation/send', [App\Http\Controllers\ContactsController::class, 'send']);