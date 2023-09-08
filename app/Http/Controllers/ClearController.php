<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class ClearController extends Controller
{
    public function __invoke(): string
    {
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        Artisan::call('view:clear');
        return "Cleared!";
    }
}
