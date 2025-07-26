<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Home;
use Illuminate\Support\Facades\Response;

Route::get('/', Home::class)->name('home');

Route::get('/descargar-codigo', function () {
    $codigo = request('codigo');

    $filename = 'codigo_exportado.cpp';
    $headers = [
        'Content-Type' => 'text/plain',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    return Response::make($codigo, 200, $headers);
})->name('descargar');