<?php
// Create with: php artisan make:command FixMalformedImageUrls

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;

class FixMalformedImageUrls extends Command
{
    protected $signature = 'fix:malformed-urls';
    protected $description = 'Fix malformed image URLs in events table';

    public function handle()
    {
        $this->info('Scanning for events with malformed image URLs...');

        $events = Event::whereNotNull('cover_photo')->get();
        $fixed = 0;

        foreach ($events as $event) {
            $originalUrl = $event->cover_photo;
            
            // Check if URL contains /tmp/ (temporary file) or double slashes
            if (str_contains($originalUrl, '/tmp/') || str_contains($originalUrl, '//storage/')) {
                $this->warn("Event {$event->event_id}: Found malformed URL: {$originalUrl}");
                
                // Option 1: Clear the malformed URL
                $event->update(['cover_photo' => null]);
                $this->info("  -> Cleared malformed URL");
                $fixed++;
                
                // Option 2: If you want to try to fix it instead of clearing:
                // $cleanUrl = $this->attemptToFixUrl($originalUrl);
                // if ($cleanUrl !== $originalUrl) {
                //     $event->update(['cover_photo' => $cleanUrl]);
                //     $this->info("  -> Fixed URL to: {$cleanUrl}");
                //     $fixed++;
                // }
            }
        }

        $this->info("Fixed {$fixed} malformed URLs");
        
        // Also check for events that should be using Cloudinary
        $this->info("\nChecking for events that need Cloudinary migration...");
        $needsMigration = Event::whereNotNull('cover_photo')
            ->whereNull('cover_photo_url')
            ->where('cover_photo', 'not like', '%/tmp/%')
            ->count();
            
        if ($needsMigration > 0) {
            $this->warn("Found {$needsMigration} events with local images that should be migrated to Cloudinary");
            $this->info("Run 'php artisan migrate:cloudinary' to migrate them");
        }
    }

    private function attemptToFixUrl($url)
    {
        // Remove duplicate slashes
        $url = preg_replace('/\/+/', '/', $url);
        
        // Fix protocol
        if (!str_starts_with($url, 'http')) {
            $url = 'https:/' . ltrim($url, '/');
        }
        
        return $url;
    }
}