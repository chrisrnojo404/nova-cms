<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    public function definition(): array
    {
        $filename = Str::uuid()->toString().'.jpg';
        $path = 'media/images/'.$filename;

        return [
            'user_id' => User::factory(),
            'disk' => 'public',
            'directory' => 'media/images',
            'filename' => $filename,
            'original_name' => 'sample-image.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'path' => $path,
            'url' => '/storage/'.$path,
            'size' => 102400,
            'alt_text' => 'Sample media asset',
        ];
    }
}
