<?php

namespace App\Actions;

use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneratePDF
{
    public function execute(string $view, array $data, string $orientation = 'portrait')
    {
        $html = view($view, $data)->render();

        $Arabic = new Arabic;
        $arabicSegments = $Arabic->arIdentify($html);
        for ($i = count($arabicSegments) - 1; $i >= 0; $i -= 2) {
            $utf8ar = $Arabic->utf8Glyphs(substr($html, $arabicSegments[$i - 1], $arabicSegments[$i] - $arabicSegments[$i - 1]));
            $html = substr_replace($html, $utf8ar, $arabicSegments[$i - 1], $arabicSegments[$i] - $arabicSegments[$i - 1]);
        }

        // Ensure remote assets (https images) can be fetched by Dompdf
        Pdf::setOptions(['isRemoteEnabled' => true]);

        return Pdf::loadHTML($html)->setPaper('a4', $orientation);
    }
}
