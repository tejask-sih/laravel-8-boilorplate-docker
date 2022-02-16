<?php

use Illuminate\Support\Facades\Route;
use Dingo\Api\Routing\Router;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function (Router $api) {
    $api->get('/', function () {
        echo 'Welcome to project11';
    });
});

// Route::get('/', function () {
//     return view('welcome');
// });
