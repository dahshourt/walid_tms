<?php

use App\Http\Controllers\ChangeRequest\Api\EmailApprovalController;
use App\Http\Controllers\ChangeRequest\Api\TechnicalFeedbackController;
use Illuminate\Support\Facades\Route;

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

// Technical Feedback API Route - Using manual header-based authentication
Route::post('/technical-feedback', [TechnicalFeedbackController::class, 'addTechnicalFeedback']);
Route::post('/email-approval', [EmailApprovalController::class, 'UpdateEmailApprovel']);
