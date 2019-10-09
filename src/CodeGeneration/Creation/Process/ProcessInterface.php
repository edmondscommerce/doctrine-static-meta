<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

/**
 * Processes are objects that transform the contents of a File object
 */
interface ProcessInterface
{
    public function run(File\FindReplace $findReplace): void;
}
