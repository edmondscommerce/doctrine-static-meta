<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\FindReplace;

class FindReplaceFactory
{
    public function create(File $file): FindReplace
    {
        return new FindReplace($file);
    }
}
