<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 用户登录
Route::post('/user/login', 'User@login')->name('login');
Route::get('/user/info', 'User@info')->name('userInfo');

// 获取配置信息
Route::get('/config/get', 'Config@getByName')->name('config');

// 搜索学生信息
Route::get('/students/search', 'Students@search')->name('searchStudent');

// 创建学生信息
Route::post('/students/create', 'Students@create')->name('createStudent');

// 更新学生信息
Route::post('/students/update', 'Students@update')->name('updateStudent');