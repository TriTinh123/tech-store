<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->copyImageFiles();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->copyImageFiles();
    }

    /**
     * Copy all image files from Image/ to public/images/
     */
    private function copyImageFiles(): void
    {
        $sourceDir = base_path('Image');
        $destDir = public_path('images');

        if (! is_dir($sourceDir)) {
            return;
        }

        if (! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $files = scandir($sourceDir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $sourcePath = $sourceDir.DIRECTORY_SEPARATOR.$file;
            $destPath = $destDir.DIRECTORY_SEPARATOR.$file;

            if (is_file($sourcePath)) {
                copy($sourcePath, $destPath);
            }
        }
    }
}
