<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ProfileController;
use App\Http\Controllers\Api\Admin\ClientController;
use App\Http\Controllers\Api\Admin\ProposalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\FreelancerController;

Route::prefix('v1/admin')
    ->group(function () {
        Route::controller(AuthController::class)
            ->middleware('throttle:10,1')
            ->group(function () {
                Route::post('login', 'login');
                Route::post('logout', 'logout')->middleware('auth:sanctum');
            });

        Route::middleware('auth:sanctum')
            ->prefix('auth')
            ->group(function () {
                Route::controller(DashboardController::class)
                    ->group(function () {
                        Route::get('dashboard', 'index');
                    });

                Route::controller(FreelancerController::class)
                    ->prefix('freelancer')
                    ->group(function () {
                        Route::get('', 'index')->name('index');
                    });

                Route::controller(ProfileController::class)
                    ->prefix('profile')
                    ->group(function () {
                        Route::get('', 'index')->name('index');
                        Route::post('store', 'store')->name('store');
                        Route::put('{id}/update', 'update')->name('update')->where(['id' => '[0-9]+']);
                        Route::delete('{id}/delete', 'destroy')->name('destroy')->where(['id' => '[0-9]+']);
                    });

                Route::controller(ClientController::class)
                    ->prefix('client')
                    ->group(function () {
                        Route::get('', 'index')->name('index');
                    });

                Route::controller(ProposalController::class)
                    ->prefix('proposal')
                    ->group(function () {
                        Route::get('', 'index')->name('index');
                        Route::post('store', 'store')->name('store');
                        Route::put('{id}/update', 'update')->name('update')->where(['id' => '[0-9]+']);
                        Route::delete('{id}/delete', 'destroy')->name('destroy')->where(['id' => '[0-9]+']);
                    });
            });
    });
