<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function getRandomFileName(string $extension): string
    {
        return preg_replace('/[+=\/]/', random_int(0, 9), base64_encode(random_bytes(8))).'.'.$extension;
    }

    public function avatarFile(?UploadedFile $imageFile, string $targetDirectory): ?string
    {
        if ($imageFile != null) {
            // Let's create a new filename
            //  We use a little homemade algorithm that makes a random string of characters
            $filename = $this->getRandomFileName($imageFile->getClientOriginalExtension());

            $imageFile->move('images/'.$targetDirectory, $filename);

            return $filename;
        } else {
            return null;
        }
    }
}