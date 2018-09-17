<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

class Writer
{
    /**
     * Write a file object to the filesystem and return the created path
     *
     * @param File $file
     *
     * @return string
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function write(File $file): string
    {
        $this->createDirectoryIfRequired($file);
        $file->create();
        $file->putContents();

        return $file->getPath();
    }

    private function createDirectoryIfRequired(File $file): void
    {
        $directory = $file->getDirectory();
        if (false === $directory->exists()) {
            $directory->create();
        }
    }
}