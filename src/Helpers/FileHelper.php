<?php

declare(strict_types=1);

namespace App\Helpers;

class FileHelper
{
    public $date;

    const IMAGE_PATH = __DIR__ . '/../../../../cooler/images/';

    public function __construct()
    {
        $this->date = date('d-m-Y. H-i-s');
    }

    public function saveTempFile($imageString)
    {
        file_put_contents(self::IMAGE_PATH . 'temp.jpeg', base64_decode($imageString));
        unset($imageString);
    }

    public function copyToConstantFile($imageName)
    {
        copy(IMAGE_PATH . 'temp.jpeg', IMAGE_PATH . $imageName);
    }
}
