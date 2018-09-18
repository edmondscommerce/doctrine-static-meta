<?php

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Repositories;


use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\AbstractEntityRepositoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

class AbstractEntityRepositoryCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function itWontLetYouPassAnewObjectFqn()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You should not pass a new object FQN to this creator');
        $this->getCreator()->createTargetFileObject('\\Some\\Fqn');
    }

    private function getCreator(): AbstractEntityRepositoryCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new AbstractEntityRepositoryCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory()
        );
    }

    /**
     * @test
     */
    public function itCanCreateANewAbstractEntityFactory(): void
    {
        $file     = $this->getCreator()
                         ->setProjectRootNamespace('My\\Test\\Project')
                         ->createTargetFileObject()
                         ->getTargetFile();
        $expected = '<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Repositories;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class AbstractEntityRepository extends DSM\Repositories\AbstractEntityRepository
{

}
';
        $actual   = $file->getContents();
        self::assertSame($expected, $actual);

    }
}
