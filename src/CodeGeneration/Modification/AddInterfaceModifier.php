<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

class AddInterfaceModifier
{
    public function setClassFileImplementsInterface(File $classFile, string $interfaceFqn): void
    {
        $contents = $classFile->getContents();
        $this->addUseStatement($contents);
    }

    private function addUseStatement(string $contents)
    {
        $useBlock=preg_match('%namespace.+?;(^[^;]+?;$)+%s')
    }
}