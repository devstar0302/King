<?php

use Illuminate\Support\Facades\Route;

Route::post('categories', 'CategoryController@saveTable');
Route::get('categories/delete/{id}', 'CategoryController@destroy');
Route::resource('categories', 'CategoryController', ['except' => ['show', 'store', 'destroy']]);

Route::resource('tutorial', 'TutorialController');

Route::get('paragraphs/delete/{id}', 'ParagraphController@destroy');
Route::post('paragraphs/{id}/edit', 'ParagraphController@saveTable');
Route::resource('paragraphs', 'ParagraphController', ['except' => ['show', 'store', 'destroy']]);

Route::get('/', 'HomeController@index')->name('home');
Route::get('/artisan', 'HomeController@artisan');

Route::get('malfunctions/test', 'MalfunctionController@get');
Route::get('malfunctions/{id}/destroy', 'MalfunctionController@destroy');
Route::post('malfunctions/sharePdf', 'MalfunctionController@ajaxSendPdf')->name('malfunctionSharePdf');
Route::post('malfunctions/changeStatus', 'MalfunctionController@ajaxChangeStatus')->name('changeStatus');
Route::post('malfunctions/duplicate', 'MalfunctionController@ajaxDuplicateMalfunction')->name('duplicateMalfunction');
Route::post('malfunctions/{id}/saveComments', 'MalfunctionController@ajaxMalfunctionSaveComments')->name('malfunctionSaveComments');
Route::get('malfunctions/guidance/create', 'MalfunctionController@createGuidance')->name('createGuidance');
Route::get('malfunctions/guidance/{id}', 'MalfunctionController@showGuidance')->name('showGuidance');
Route::get('malfunctions/guidance/{id}/edit', 'MalfunctionController@editGuidance')->name('editGuidance');
Route::put('malfunctions/guidance/{id}/update', 'MalfunctionController@updateGuidance')->name('updateGuidance');
Route::get('malfunctions/guidance/{id}/destroy', 'MalfunctionController@destroyGuidance')->name('destroyGuidance');
Route::post('malfunctions/guidance/{id}/saveComments', 'MalfunctionController@ajaxGuidanceSaveComments')->name('guidanceSaveComments');
Route::post('malfunctions/guidance/duplicate', 'MalfunctionController@ajaxDuplicateGuidance')->name('duplicateGuidance');
Route::resource('malfunctions', 'MalfunctionController', ['except' => ['destroy']]);
Route::post('filterCompany', 'MalfunctionController@filterCompany');
Route::post('filterSite', 'MalfunctionController@filterSite');
Route::post('filterSubsite', 'MalfunctionController@filterSubsite');
Route::post('find', 'MalfunctionController@find');
Route::post('level','MalfunctionController@level');

Route::post('/upload-file', 'HomeController@storeFile');
Route::post('/upload-files', 'HomeController@simpleStoreFile');
Route::post('/upload-sf-files', 'HomeController@simpleStoreFileSF');

Auth::routes();

//    Route::get('/home', 'HomeController@dashboard')->name('home');
Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

Route::get('users/gen-password', 'UserController@ajaxGenPassword')->name('gen-password');
Route::resource('users', 'UserController');
Route::resource('companies', 'CompanyController');
Route::resource('sites', 'SiteController');
Route::resource('subsites', 'SubSiteController');

Route::get('/send-credentials/{id}', 'HomeController@mail')->name('send-cred');
Route::get('/get-users/{id}', 'CompanyController@getUserByRoleId')->name('users-by-type');
Route::get('/get-sites/{id}', 'SiteController@getSitesById')->name('sites-by-company');
Route::get('/get-subsites/{id}', 'SubSiteController@getSitesById')->name('subsites-by-site');

Route::post('/links', 'LinkController@store')->name('links.store');
Route::delete('/links/{id}', 'LinkController@destroy')->name('links.destroy');

Route::post('change', 'CompanyController@change')->name('change');

Route::pattern('id', '[0-9]+');

Route::get('/nik/', 'Frontend\HomeController@index');
Route::get('/nik/print/{id}', 'Frontend\HomeController@printFile');

Route::post('/nik/delete', 'Frontend\BaseController@deleteFile');
Route::post('/nik/upload', 'Frontend\BaseController@uploadFile');

Route::post('/nik/newArea', 'Frontend\BaseController@newAreaHtml');

Route::post('/nik/newsort', 'Frontend\BaseController@newsort');

Route::post('/nik/sendmail', 'Frontend\BaseController@sendmail');

//Route::get('get-comment-list', 'CommentController@ajaxGetComments')->name('get-comment-list');
//Route::post('add-new-comment', 'CommentController@ajaxCreateNewComment')->name('add-new-comment');
//Route::post('update-comment', 'CommentController@ajaxUpdateComment')->name('update-comment');
//Route::post('delete-comment', 'CommentController@ajaxDeleteComment')->name('delete-comment');

Route::get('statistics', 'StatisticsController@index');
Route::get('statistics/get', 'StatisticsController@ajaxGetStatistics')->name('get-statistics');
Route::post('statistics/sharePdf', 'StatisticsController@ajaxSendPdf')->name('statisticSharePdf');

Route::post('lang/{lang}', ['as' => 'lang.switch', 'uses' => 'LanguageController@switchLang']);

Route::get('/tutorial-videos/', 'TutorialVideosController@index');
Route::post('/tutorial/send-email', 'TutorialController@sendEmail');

Route::post('/malfunctions/makegrid', 'MalfunctionController@makeGrid');
