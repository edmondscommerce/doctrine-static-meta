<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Tests;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\BootstrapCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\BootstrapCreator
 * @small
 */
class BootstrapCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function itCanCreateTheBootstrap(): void
    {
        $expected = \ts\file_get_contents(__DIR__ . '/../../../../../codeTemplates/tests/bootstrap.php');
        $actual   = $this->getCreator()->createTargetFileObject()->getTargetFile()->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): BootstrapCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        $creator = new BootstrapCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory()
        );
        $creator->setProjectRootDirectory(AbstractTest::VAR_PATH);

        return $creator;
    }
}
