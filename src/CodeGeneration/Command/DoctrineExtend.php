<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

class DoctrineExtend
{
    public static function getCommands(): array
    {
        return [
            new GenerateRelationsCommand(),
            new GenerateEntityCommand(),
            new SetRelationCommand()
        ];
    }
}
