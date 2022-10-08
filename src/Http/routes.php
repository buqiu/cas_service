<?php

use Buqiu\Cas\Http\Controllers\SecurityController;
use Buqiu\Cas\Http\Controllers\ValidateController;
use Illuminate\Support\Facades\Route;

$options = [
    'prefix' => config('cas.router.prefix'),
];

if (config('cas.middleware.common')) {
    $options['middleware'] = config('cas.middleware.common');
}

Route::group($options, function () {
    $auth = config('cas.middleware.auth');
    $p = config('cas.router.name_prefix');
    Route::get('login', [SecurityController::class, 'showLogin'])->name($p.'login.get');
    Route::post('login', [SecurityController::class, 'login'])->name($p.'login.post');
    Route::get('logout', [SecurityController::class, 'logout'])->name($p.'login.logout')->middleware($auth);
    Route::get('p3/login', [SecurityController::class, 'showLogin'])->name($p.'v3.login.get');
    Route::post('p3/login', [SecurityController::class, 'login'])->name($p.'v3.login.post');
    Route::get('p3/logout', [SecurityController::class, 'logout'])->name($p.'v3.logout')->middleware($auth);
    Route::any('checkAuth', [ValidateController::class, 'checkAuth'])->name($p.'v1.checkAuth');
    Route::any('validate', [ValidateController::class, 'v1ValidateAction'])->name($p.'v1.validate');
    Route::any('serviceValidate', [ValidateController::class, 'v2ServiceValidateAction'])->name($p.'v2.validate.service');
    Route::any('proxyValidate', [ValidateController::class, 'v2ProxyValidateAction'])->name($p.'v2.validate.proxy');
    Route::any('proxy', [ValidateController::class, 'proxyAction'])->name($p.'proxy');
    Route::any('p3/serviceValidate', [ValidateController::class, 'v3ServiceValidateAction'])->name($p.'v3.validate.service');
    Route::any('p3/proxyValidate', [ValidateController::class, 'v3ProxyValidateAction'])->name($p.'v3.validate.proxy');
});



