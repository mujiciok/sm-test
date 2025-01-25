<?php

use App\Http\Controllers as Controllers;
use Illuminate\Support\Facades\Route;

Route::match(['GET', 'POST'], 'keyword-insights', Controllers\KeywordInsightController::class);
