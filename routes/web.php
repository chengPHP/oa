<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::group(['prefix' => 'admin','namespace' => 'Admin'],function ($router)
{
    $router->get('login', 'LoginController@showLogin')->name('admin.login');
    $router->post('login', 'LoginController@login');
    $router->post('logout', 'LoginController@logout');

//    $router->get('index', 'AdminController@index');
});

Route::group(['prefix' => 'admin','namespace' => 'Admin'],function ($router)
{
    $router->get('index', 'IndexController@index');
    //后台用户管理
    Route::resource('manager','AdminController');
    //角色管理
    Route::resource('role','RoleController');
    //权限管理
    Route::resource('permission','PermissionController');
    //前台用户管理
    Route::resource('user','UserController');
    //商品类别管理
    Route::resource('category','CategoryController');
    //商品管理
    Route::resource('product','ProductController');
});

//文件管理模块路由开始
//-------------------------------------------------------------------------
Route::group(['prefix' => 'file', 'namespace' => 'Admin'], function () {
    Route::post('image_upload', 'FileController@imageUpload')->name('image.upload');
    Route::post('file_upload', 'FileController@fileUpload')->name('file.upload');
    Route::post('video_upload', 'FileController@videoUpload')->name('video.upload');
});
