<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
        return preg_replace('/[+=\/]/', random_int(0, 9), base64_encode(random_bytes(8))) . '.' . $extension;
    }

    public function avatarFile(?UploadedFile $imageFile, string $targetDirectory): ?string
    {
        if ($imageFile != null) {
            // Créons un nouveau nom de fichier
            // On utilise un petit algorithme fait maison qui nous fait une chaine de caractère aléatoire
            $filename = $this->getRandomFileName($imageFile->getClientOriginalExtension());

            $imageFile->move($this->getTargetDirectory(), $filename);

            return $filename;
        } else {

            return null;
        }
    }


    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
