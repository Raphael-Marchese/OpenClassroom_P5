<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\File\File;

class ImageFactory
{
    public function createImage($array): File
    {
        $name = $array['name'] ?? null;
        $size = $array['size'] ?? null;
        $tmpName = $array['tmp_name'] ?? null;

        return new File(name: $name, size: $size, tmpName: $tmpName);
    }
}