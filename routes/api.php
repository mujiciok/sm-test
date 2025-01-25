<?php

use App\Http\Controllers as Controllers;
use Illuminate\Support\Facades\Route;

Route::match(['GET', 'POST'], 'keyword-visibility', Controllers\KeywordVisibilityController::class);
Route::match(['GET', 'POST'], 'keyword-insight', Controllers\KeywordInsightController::class);
