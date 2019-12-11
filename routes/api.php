<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('details', 'API\UserController@details');

    Route::group(['prefix'=>'user'], function(){	
		Route::post('/editProfile','API\UserController@editProfile');
		Route::post('/historyQuestionActivity/{id}','API\UserController@getUserQuestionHistory'); //Route menampilkan seluruh history pertanyaan dan jawaban user
		Route::post('/historyAnswerActivity/{id}','API\UserController@getUserAnswerHistory'); //Route menampilkan seluruh history pertanyaan dan jawaban user
		Route::post('/historyAnswerActivity/delete/{id}','API\UserController@deleteAnswer');
		Route::post('/historyQuestionActivity/delete/{id}','API\UserController@deleteQuestion');
		Route::get('/historyQuestionActivity/edit/{id}','API\UserController@showEditQuestion');
		Route::post('/historyQuestionActivity/update/{id}','API\UserController@editQuestion');
		Route::get('/countQuestionAndAnswer/{id}','API\UserController@countQuestionAndAnswer');
	});
    
    Route::group(['prefix'=>'kategori'], function(){	
        Route::post('/addKategori','API\KategoriController@addKategori');
		Route::get('/getAllKategori','API\KategoriController@getAllKategori');
		Route::post('/editKategori','API\KategoriController@editKategori');
    });

    Route::group(['prefix'=>'pertanyaan'], function(){
		Route::post('/postPertanyaan','API\PertanyaanController@postPertanyaan'); //Route post Pertanyaan
		Route::post('/showTrendingPertanyaan','API\PertanyaanController@showTrendingPertanyaan'); //Route menampilkan seluruh pertanyaan pada Home App
		Route::post('/showLatestPertanyaan','API\PertanyaanController@showLatestPertanyaan');
		
		Route::get('/showDetailPertanyaan/{id}','API\PertanyaanController@showDetailPertanyaan'); 
		Route::post('/onClickIncrease/{id}','API\PertanyaanController@onClickIncrease');

		Route::post('/postJawaban','API\JawabanController@postComment');
		Route::get('/showJawaban/{id}','API\JawabanController@showComment');
		Route::post('/showJawaban/like/{id}','API\JawabanController@commentLike');

		Route::post('/showPertanyaanPerKategori/{id}','API\PertanyaanController@showPertanyaanPerKategori');
		
		// Route::get('/topOfUser','API\TopOfUserController@getTopOfUser');
		
		Route::post('/showUpdatePertanyaan','API\PertanyaanController@showUpdatePertanyaan'); //Route menampilkan data sebelum update
		Route::post('/storeUpdatePertanyaan','API\PertanyaanController@storeUpdatePertanyaan'); //Route post pertanyaan

	});
    
});
