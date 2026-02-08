<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DocumentController;

Route::post('/documents', [DocumentController::class, 'store']);


Route::get('/', function () {
    return view('welcome');
});

use App\Models\Document;

Route::get('/panel', function () {

    $documents = Document::all();

    return view('documents', compact('documents'));

});
