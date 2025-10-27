<?php
use Illuminate\Support\Facades\Route;

Route::prefix('propietarios')->name('api.propietarios.')->group(function() {
    require base_path('routes/propietarios/propietario.php');
});