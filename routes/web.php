<?php


use Illuminate\Support\Facades\Route;
use App\Models\Document;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/panel', function () {

    $documents = Document::all();

    return view('documents', compact('documents'));

});
