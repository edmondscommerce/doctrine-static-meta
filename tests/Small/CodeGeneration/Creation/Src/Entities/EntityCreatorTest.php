<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entities;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entities\EntityCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

class EntityCreatorTest extends TestCase
{
    public function itCanCreateANewEntity(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\TestEntity';
        $file         = $this->getEntityCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = '';
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    public function itCanCreateADeeplyNamespaceNewEntity(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\Deeply\\Namespaced\\TestEntity';
        $file         = $this->getEntityCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = '';
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getEntityCreator(): EntityCreator
    {
        return new EntityCreator(
            new FileFactory($namespaceHelper, new Config(ConfigTest::SERVER)),
            new FindReplaceFactory(),
            $namespaceHelper,
            new Writer()
        );
    }
}