<?php

use App\Http\Controllers\Api\FieldTranslationController;
use Illuminate\Support\Facades\Route;

// Field level auto-translation endpoints (Google Translate AR <-> EN)
Route::post('/translate/field', [FieldTranslationController::class, 'translateField'])->name('api.translate.field');
Route::post('/translate/batch', [FieldTranslationController::class, 'translateBatch'])->name('api.translate.batch');

// Load API routes from modules
foreach (glob(app_path('Modules/*/Routes/api.php')) as $routeFile) {
    require $routeFile;
}
