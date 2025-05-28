<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class MigrateEventImagesToCloudinary extends Command
{
    protected $signature = 'events:migrate-images';
    protected $description = 'Migrate existing event cover photos to Cloudinary';

    public function handle()
    {
        $cloudinaryService = app(CloudinaryService::class);
        
        $events = Event::whereNotNull('cover_photo')
            ->whereNull('cover_photo_url')
            ->get();

        $this->info("Found {$events->count()} events to migrate");

        foreach ($events as $event) {
            try {
                $this->info("Migrating event: {$event->event_name}");
                
                // Skip if it's already a URL
                if (filter_var($event->cover_photo, FILTER_VALIDATE_URL)) {
                    $this->warn("Skipping URL: {$event->cover_photo}");
                    continue;
                }

                // Check if file exists in storage
                $filePath = storage_path('app/public/' . $event->cover_photo);
                if (!file_exists($filePath)) {
                    $this->error("File not found: {$filePath}");
                    continue;
                }

                // Create an UploadedFile instance
                $uploadedFile = new UploadedFile(
                    $filePath,
                    basename($event->cover_photo),
                    mime_content_type($filePath),
                    null,
                    true
                );

                // Upload to Cloudinary
                $uploadResult = $cloudinaryService->upload($uploadedFile, 'event_covers');

                if ($uploadResult) {
                    $event->update([
                        'cover_photo_url' => $uploadResult['url'],
                        'cover_photo_public_id' => $uploadResult['public_id']
                    ]);
                    
                    $this->info("✓ Migrated: {$event->event_name}");
                } else {
                    $this->error("✗ Failed to upload: {$event->event_name}");
                }

            } catch (\Exception $e) {
                $this->error("✗ Error migrating {$event->event_name}: " . $e->getMessage());
            }
        }

        $this->info('Migration completed!');
    }
}