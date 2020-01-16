<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileManager
 */
class FileManager
{
    /**
     * @param UploadedFile $uploadedFile
     * @param string $destination
     * @return string
     */
    public function uploadFile(UploadedFile $uploadedFile, string $destination)
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = \transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename
        );
        $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

        $uploadedFile->move(
            $destination,
            $newFilename
        );

        return $newFilename;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    public function deleteFile(string $filePath)
    {
        if (file_exists($filePath)) {
            try {
                unlink($filePath);
            } catch (\Exception $exception) {
                return false;
            }
        }

        return true;
    }
}
