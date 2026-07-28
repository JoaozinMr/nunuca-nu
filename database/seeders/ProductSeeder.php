<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Database\Factories\ProductFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Copy the original mockup images into Laravel's public storage
        $this->publishImages();

        foreach (ProductFactory::$catalogProducts as $data) {
            Product::updateOrCreate(
                ['name' => $data['name'], 'flavor' => $data['flavor']],
                $data
            );
        }
    }

    /**
     * Copy images from the /nunuca/uploads reference folder into storage.
     * Image filenames in the factory are canonical; we map them from the
     * original long filenames used in the HTML mockup.
     */
    private function publishImages(): void
    {
        $sourceDir = base_path('../../Desktop/nunuca/uploads');
        $destDisk  = Storage::disk('public');

        if (! File::isDirectory($sourceDir)) {
            $this->command->warn("Reference image directory not found at {$sourceDir}. Skipping image copy.");
            return;
        }

        $destDisk->makeDirectory('products');

        $map = [
            // original filename from /nunuca/uploads → canonical storage name
            '640388649_17853254505675357_2104312982336529400_n.jpg' => 'products/copo-brigadeiro.jpg',
            '649235888_17854436478675357_3388398832682660146_n.jpg' => 'products/alfajor-doce-de-leite.jpg',
            '631945457_17848704696675357_5264883073846836857_n.jpg' => 'products/palha-brigadeiro.jpg',
            '628045118_17848974363675357_8802926290949794625_n.jpg' => 'products/palha-oreo.jpg',
        ];

        foreach ($map as $src => $dest) {
            $srcPath = $sourceDir . DIRECTORY_SEPARATOR . $src;
            if (File::exists($srcPath) && ! $destDisk->exists($dest)) {
                $destDisk->put($dest, File::get($srcPath));
                $this->command->info("Copied: {$dest}");
            }
        }
    }
}
