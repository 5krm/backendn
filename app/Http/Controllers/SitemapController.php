<?php

namespace App\Http\Controllers;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Home page
        $sitemap .= $this->addUrl(route('home'), '1.0', 'daily');

        // Courses
        $sitemap .= $this->addUrl(route('courses'), '0.9', 'daily');

        // Legal pages
        $sitemap .= $this->addUrl(route('legal.faq'), '0.6', 'monthly');
        $sitemap .= $this->addUrl(route('legal.privacy-policy'), '0.6', 'monthly');
        $sitemap .= $this->addUrl(route('legal.terms-of-service'), '0.6', 'monthly');
        $sitemap .= $this->addUrl(route('legal.cookie-policy'), '0.6', 'monthly');
        $sitemap .= $this->addUrl(route('legal.contact'), '0.7', 'monthly');

        $sitemap .= '</urlset>';

        return response($sitemap, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    private function addUrl($url, $priority = '0.5', $changefreq = 'weekly')
    {
        return sprintf(
            "  <url>\n    <loc>%s</loc>\n    <priority>%s</priority>\n    <changefreq>%s</changefreq>\n  </url>\n",
            htmlspecialchars($url),
            $priority,
            $changefreq
        );
    }
}
