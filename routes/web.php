<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// API route group
$router->group(['prefix' => 'api'], function () use ($router) {

    $router->post('register', 'AuthController@register');
    $router->get('get_token', 'AuthController@getToken');

    $router->post('get_antrean', 'AntreanController@create');
    $router->post('get_rekap_antrean', 'AntreanController@recap');
    $router->post('get_antrean_tanggal', 'AntreanController@list_antrean');
    
    $router->group(['prefix'=>'antrean'],function() use ($router){
        $router->post('status','AntreanController@status');
        $router->post('ambil','AntreanController@ambil');
        $router->post('sisa','AntreanController@sisa');
        $router->post('check_in','AntreanController@check_in');
        $router->post('batal','AntreanController@batal');
        $router->post('info-pasien-baru','AntreanController@info_pasien_baru');
        $router->post('i_care','AntreanController@i_care');
    });

    $router->post('add_operasi', 'OperasiController@create');
    $router->post('get_operasi', 'OperasiController@recap_peserta');
    $router->post('get_operasi_jadwal', 'OperasiController@recap_schedule');

    $router->post('get_poli', 'PoliController@index');
    $router->post('add_poli', 'PoliController@create');

});
