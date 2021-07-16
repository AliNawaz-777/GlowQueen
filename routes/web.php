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
Route::get('send_mail', function(){
                    echo "string";
                });
Route::get('/my-commands', function() {
    Artisan::call('storage:link');
    return "Storage link";
    });

Route::get('/clear-cache', function () {
Artisan::call('cache:clear'); //storage:link
// Artisan::call('route:cache');
Artisan::call('route:clear');
Artisan::call('view:clear');
Artisan::call('config:clear');
Artisan::call('config:cache');
// Artisan::call('storage:link');
echo "done";
});

Route::get('/tinker', function() {
    Artisan::call('tinker');
    return "tinker";
    });

Route::get('/optimize', function ()
{
	Artisan::call('optimize');
	return 'optimize';
});

Route::get("/sitemap.xml", function (){
    // $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT']."/sitemap.xml"); // PATH TO YOUR FILE.
    // echo "<pre>"; print_r($xml); "</pre>";
    include $_SERVER['DOCUMENT_ROOT']."/sitemap.xml";
});

// Route::get('page/send_mail', function(){
//                     echo "string";
//                 });