<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $sitemap = $this->generateSitemap();
        
        return response($sitemap, 200)
            ->header('Content-Type', 'text/xml');
    }

    private function generateSitemap(): string
    {
        $baseUrl = config('app.url');
        $now = Carbon::now()->toAtomString();
        
        $urls = [
            // Homepage
            $this->generateUrl($baseUrl, $now, '1.0', 'daily'),
            
            // Other static pages
            $this->generateUrl("{$baseUrl}/about", $now, '0.8', 'monthly'),
            $this->generateUrl("{$baseUrl}/contact", $now, '0.8', 'monthly'),
            $this->generateUrl("{$baseUrl}/plans", $now, '0.9', 'weekly'),
            $this->generateUrl("{$baseUrl}/blog", $now, '0.9', 'weekly'),
        ];

        // Add blog posts if Blog model exists
        if (class_exists(Blog::class)) {
            $blogs = Blog::published()->latest()->get();
            foreach ($blogs as $blog) {
                $urls[] = $this->generateUrl(
                    "{$baseUrl}/blog/{$blog->slug}",
                    $blog->updated_at->toAtomString(),
                    '0.8',
                    'monthly'
                );
            }
        }

        return view('sitemap.index', [
            'urls' => $urls,
            'baseUrl' => $baseUrl,
        ])->render();
    }

    private function generateUrl(string $loc, string $lastmod, string $priority, string $changefreq): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
