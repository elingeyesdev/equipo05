<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ScrapeIncendiosNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:incendios-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape fire-related news from opinion.com.bo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to scrape news from opinion.com.bo...');

        try {
            $response = Http::timeout(30)->get('https://www.opinion.com.bo/tags/incendios/');

            if (!$response->successful()) {
                $this->error('Failed to fetch the page');
                return 1;
            }

            $html = $response->body();

            // Parse the HTML to extract news articles
            $articles = $this->parseArticles($html);

            $this->info('Found ' . count($articles) . ' articles');

            $newCount = 0;
            $updatedCount = 0;

            foreach ($articles as $article) {
                // Check if article already exists by URL
                $existing = DB::connection('cuadrillas')->table('noticia')->where('url', $article['url'])->first();

                if ($existing) {
                    // Update existing article
                    DB::connection('cuadrillas')->table('noticia')->where('id_noticia', $existing->id_noticia)->update([
                        'titulo' => $article['title'],
                        'descripcion' => $article['description'],
                        'image' => $article['image'],
                        'date' => $article['date'],
                        'updated_at' => now(),
                    ]);
                    $updatedCount++;
                } else {
                    // Create new article
                    DB::connection('cuadrillas')->table('noticia')->insert([
                        'titulo' => $article['title'],
                        'url' => $article['url'],
                        'descripcion' => $article['description'],
                        'image' => $article['image'],
                        'date' => $article['date'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $newCount++;
                }
            }

            $this->info("Scraping completed! New: {$newCount}, Updated: {$updatedCount}");
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Parse HTML to extract article information
     */
    private function parseArticles($html)
    {
        $articles = [];

        // Pattern to match article links - both full and relative URLs
        preg_match_all('/<a[^>]+href="((?:https:\/\/www\.opinion\.com\.bo)?\/articulo\/[^"]+)"[^>]*>(.*?)<\/a>/s', $html, $linkMatches, PREG_SET_ORDER);

        $this->info('Found ' . count($linkMatches) . ' potential article links');

        $processedUrls = [];
        $count = 0;

        foreach ($linkMatches as $match) {
            $url = $match[1];
            $linkContent = $match[2];

            // Convert relative URLs to absolute
            if (strpos($url, 'http') !== 0) {
                $url = 'https://www.opinion.com.bo' . $url;
            }

            // Skip if already processed
            if (isset($processedUrls[$url])) {
                continue;
            }

            // Extract title from the link content
            $title = trim(strip_tags($linkContent));

            // Skip if title is too short or empty
            if (strlen($title) < 10) {
                continue;
            }

            $processedUrls[$url] = true;

            // Extract date from URL (format: /YYYYMMDDHHMMSS)
            $date = $this->extractDateFromUrl($url);

            $this->info("Processing: " . substr($title, 0, 60) . "...");

            // Try to fetch the article page to get image and description
            $articleData = $this->fetchArticleDetails($url);

            $articles[] = [
                'url' => $url,
                'title' => $title,
                'description' => $articleData['description'] ?? substr($title, 0, 200),
                'image' => $articleData['image'] ?? null,
                'date' => $date,
            ];

            $count++;

            // Limit to 10 articles
            if ($count >= 10) {
                break;
            }

            // Small delay to avoid overwhelming the server
            usleep(500000); // 0.5 seconds
        }

        return $articles;
    }

    /**
     * Extract date from URL
     */
    private function extractDateFromUrl($url)
    {
        // URL format: .../20251028172000982601.html
        if (preg_match('/\/(\d{14})\d+\.html/', $url, $matches)) {
            $dateStr = $matches[1];
            try {
                return Carbon::createFromFormat('YmdHis', $dateStr);
            } catch (\Exception $e) {
                return now();
            }
        }

        return now();
    }

    /**
     * Fetch article details (image and description)
     */
    private function fetchArticleDetails($url)
    {
        try {
            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) {
                return ['image' => null, 'description' => null];
            }

            $html = $response->body();

            $image = null;
            $description = null;

            // Try to extract Open Graph image
            if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\'](https?:\/\/[^"\']+)["\']/', $html, $matches)) {
                $image = $matches[1];
            }

            // Try to extract meta description
            if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']/', $html, $matches)) {
                $description = trim(strip_tags($matches[1]));
            } elseif (preg_match('/<meta[^>]+property=["\']og:description["\'][^>]+content=["\'](.*?)["\']/', $html, $matches)) {
                $description = trim(strip_tags($matches[1]));
            }

            return [
                'image' => $image,
                'description' => $description,
            ];
        } catch (\Exception $e) {
            return ['image' => null, 'description' => null];
        }
    }
}
