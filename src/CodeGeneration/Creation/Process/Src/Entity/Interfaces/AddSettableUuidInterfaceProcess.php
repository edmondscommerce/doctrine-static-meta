<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ProcessInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\UuidPrimaryKeyInterface;

class AddSettableUuidInterfaceProcess implements ProcessInterface
{
    public function run(File\FindReplace $findReplace): void
    {
        $findReplace->findReplace(
            'use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;',
            "use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;\n" .
            'use ' . UuidPrimaryKeyInterface::class . ';'
        )->findReplace(
            '    DSM\Interfaces\EntityInterface',
            "    DSM\Interfaces\EntityInterface,\n    UuidPrimaryKeyInterface"
        );
    }
}
