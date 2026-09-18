<?php

use Illuminate\Support\Facades\Route;

// Load API routes from modules
foreach (glob(app_path('Modules/*/Routes/api.php')) as $routeFile) {
    require $routeFile;
}
