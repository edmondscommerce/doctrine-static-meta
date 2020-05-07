<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

class ReplaceNameProcess implements ProcessInterface
{

    /**
     * @var string
     */
    protected string $findName;
    /**
     * @var string
     */
    protected string $newObjectBaseName;

    public function setArgs(string $findName, string $newObjectBaseName): ReplaceNameProcess
    {

        $this->findName          = $findName;
        $this->newObjectBaseName = $newObjectBaseName;

        return $this;
    }

    public function run(File\FindReplace $findReplace): void
    {
        $findReplace->findReplaceName(
            $this->findName,
            $this->newObjectBaseName
        );
    }
}
