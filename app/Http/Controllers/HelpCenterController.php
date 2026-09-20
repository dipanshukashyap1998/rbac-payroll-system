<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class HelpCenterController extends Controller
{
    public function index(): View
    {
        $path = base_path('resources/docs/product-guide.md');

        if (! is_file($path)) {
            throw new RuntimeException('The product guide is missing.');
        }

        return view('help.index', [
            'title' => 'Help Center',
            'guideHtml' => Str::markdown(file_get_contents($path)),
        ]);
    }
}
