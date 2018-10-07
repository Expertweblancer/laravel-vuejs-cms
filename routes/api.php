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

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login','AuthController@authenticate');
    Route::post('/check','AuthController@check');
    Route::post('/register','AuthController@register');
    Route::get('/activate/{token}','AuthController@activate');
    Route::post('/password','AuthController@password');
    Route::post('/validate-password-reset','AuthController@validatePasswordReset');
    Route::post('/reset','AuthController@reset');
    Route::post('/social/token','SocialLoginController@getToken');
});

Route::get('/configuration/variable','ConfigurationController@getConfigurationVariable');

Route::group(['middleware' => ['jwt.auth']], function () {

    Route::post('/auth/logout','AuthController@logout');
    Route::post('/auth/lock','AuthController@lock');
    Route::get('/user/preference/pre-requisite','UserController@preferencePreRequisite');
    Route::post('/user/preference','UserController@preference');
    Route::post('/demo/message','HomeController@demoMessage');

    Route::post('/change-password','AuthController@changePassword');

    Route::post('/upload','UploadController@upload');
    Route::post('/upload/extension','UploadController@getAllowedExtension');
    Route::post('/upload/image','UploadController@uploadImage');
    Route::post('/upload/fetch','UploadController@fetch');
    Route::post('/upload/{id}','UploadController@destroy');

    Route::get('/dashboard','HomeController@dashboard');

    Route::get('/configuration','ConfigurationController@index');
    Route::post('/configuration','ConfigurationController@store');
    Route::post('/configuration/logo/{type}','ConfigurationController@uploadLogo');
    Route::delete('/configuration/logo/{type}/remove','ConfigurationController@removeLogo');
    Route::get('/fetch/lists','ConfigurationController@fetchList');

    Route::post('/backup','BackupController@store');
    Route::get('/backup','BackupController@index');
    Route::delete('/backup/{id}','BackupController@destroy');

    Route::get('/locale','LocaleController@index');
    Route::post('/locale','LocaleController@store');
    Route::get('/locale/{id}','LocaleController@show');
    Route::patch('/locale/{id}','LocaleController@update');
    Route::delete('/locale/{id}','LocaleController@destroy');
    Route::post('/locale/fetch','LocaleController@fetch');
    Route::post('/locale/translate','LocaleController@translate');
    Route::post('/locale/add-word','LocaleController@addWord');

    Route::get('/role','RoleController@index');
    Route::get('/role/{id}','RoleController@show');
    Route::post('/role','RoleController@store');
    Route::delete('/role/{id}','RoleController@destroy');

    Route::get('/permission','PermissionController@index');
    Route::get('/permission/assign/pre-requisite','PermissionController@preRequisite');
    Route::get('/permission/{id}','PermissionController@show');
    Route::post('/permission','PermissionController@store');
    Route::delete('/permission/{id}','PermissionController@destroy');
    Route::post('/permission/assign','PermissionController@assignPermission');

    Route::get('/ip-filter','IpFilterController@index');
    Route::get('/ip-filter/{id}','IpFilterController@show');
    Route::post('/ip-filter','IpFilterController@store');
    Route::patch('/ip-filter/{id}','IpFilterController@update');
    Route::delete('/ip-filter/{id}','IpFilterController@destroy');

    Route::get('/email-template','EmailTemplateController@index');
    Route::post('/email-template','EmailTemplateController@store');
    Route::get('/email-template/{id}','EmailTemplateController@show');
    Route::patch('/email-template/{id}','EmailTemplateController@update');
    Route::delete('/email-template/{id}','EmailTemplateController@destroy');
    Route::get('/email-template/{category}/lists','EmailTemplateController@lists');
    Route::get('/email-template/{id}/content','EmailTemplateController@getContent');

    Route::get('/todo','TodoController@index');
    Route::get('/todo/{id}','TodoController@show');
    Route::post('/todo','TodoController@store');
    Route::patch('/todo/{id}','TodoController@update');
    Route::delete('/todo/{id}','TodoController@destroy');
    Route::post('/todo/{id}/status','TodoController@toggleStatus');

    Route::get('/user/pre-requisite','UserController@preRequisite');
    Route::get('/user/profile/pre-requisite','UserController@profilePreRequisite');
    Route::get('/user','UserController@index');
    Route::get('/user/{id}','UserController@show');
    Route::post('/user','UserController@store');
    Route::post('/user/{id}/status','UserController@updateStatus');
    Route::patch('/user/{id}','UserController@update');
    Route::patch('/user/{id}/contact','UserController@updateContact');
    Route::patch('/user/{id}/social','UserController@updateSocial');
    Route::patch('/user/{id}/force-reset-password','UserController@forceResetPassword');
    Route::patch('/user/{id}/email','UserController@sendEmail');
    Route::post('/user/profile/update','UserController@updateProfile');
    Route::post('/user/profile/avatar/{id}','UserController@uploadAvatar');
    Route::delete('/user/profile/avatar/remove/{id}','UserController@removeAvatar');
    Route::delete('/user/{uuid}','UserController@destroy');

    Route::get('/message/compose/pre-requisite','MessageController@preRequisite');
    Route::post('/message/statistics','MessageController@statistics');
    Route::post('/message/compose','MessageController@store');
    Route::post('/message/reply','MessageController@reply');
    Route::get('/message/{uuid}/reply','MessageController@loadReply');
    Route::get('/message/draft','MessageController@getDraftList');
    Route::get('/message/{uuid}/draft','MessageController@getDraft');
    Route::get('/message/inbox','MessageController@getInboxList');
    Route::get('/message/sent','MessageController@getSentList');
    Route::get('/message/important','MessageController@getImportantList');
    Route::get('/message/trash','MessageController@getTrashList');
    Route::delete('/message/{uuid}/draft','MessageController@destroyDraft');
    Route::post('/message/{uuid}/trash','MessageController@trash');
    Route::post('/message/{uuid}/restore','MessageController@restore');
    Route::delete('/message/{id}/delete','MessageController@destroy');
    Route::get('/message/{uuid}','MessageController@show');
    Route::post('/message/{uuid}/important','MessageController@toggleImportant');

    Route::get('/email-log','EmailLogController@index');
    Route::get('/email-log/{id}','EmailLogController@show');
    Route::delete('/email-log/{id}','EmailLogController@destroy');

    Route::get('/activity-log','ActivityLogController@index');
    Route::delete('/activity-log/{id}','ActivityLogController@destroy');
});
