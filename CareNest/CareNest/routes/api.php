<?php

use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\ChildController;
use App\Http\Controllers\Api\DailyClinicSummaryController;
use App\Http\Controllers\Api\FamilyHealthHistoryController;
use App\Http\Controllers\Api\ImmunizationController;
use App\Http\Controllers\Api\MidwifeController;
use App\Http\Controllers\Api\MotherController;
use App\Http\Controllers\Api\PregnancyHistoryController;
use App\Http\Controllers\Api\PreviousPregnancyHistoryController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TestDoneController;
use App\Http\Controllers\Api\TriposhaBookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('areas', AreaController::class);
    Route::apiResource('midwives', MidwifeController::class);
    Route::apiResource('mothers', MotherController::class);
    Route::apiResource('children', ChildController::class);
    Route::apiResource('immunizations', ImmunizationController::class);
    Route::apiResource('attendances', AttendanceController::class);
    Route::apiResource('pregnancy-histories', PregnancyHistoryController::class);
    Route::apiResource('previous-pregnancy-histories', PreviousPregnancyHistoryController::class);
    Route::apiResource('family-health-histories', FamilyHealthHistoryController::class);
    Route::apiResource('tests-done', TestDoneController::class);
    Route::apiResource('triposha-books', TriposhaBookController::class);
    Route::apiResource('reports', ReportController::class);
    Route::get('daily-clinic-summaries', [DailyClinicSummaryController::class, 'index']);
    Route::get('daily-clinic-summaries/{report_id}', [DailyClinicSummaryController::class, 'show']);
});
