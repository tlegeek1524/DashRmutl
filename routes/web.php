<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemController;
Route::get('/', [SystemController::class,'index'])->name('home');
Route::get('/admin-view', [SystemController::class, 'adminview'])->name('admin.view');
Route::get('/createstudent', [SystemController::class, 'createStudent'])->name('createstudent');
Route::get('/student-view', [SystemController::class, 'studentview'])->name('student.view');
