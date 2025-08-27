<?php

use Illuminate\Support\Facades\Route;
use App\Models\Concentrate;

Route::get('/concentrates-by-major/{majorId}', function ($majorId) {
    $concentrates = Concentrate::where('intMajor_ID', $majorId)->get();
    return response()->json($concentrates);
});
