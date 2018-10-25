<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Factories;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\AbstractEntityFactoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\AbstractEntityFactoryCreator
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator
 * @small
 */
class AbstractEntityFactoryCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function itWontLetYouPassAnewObjectFqn(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You should not pass a new object FQN to this creator');
        $this->getCreator()->createTargetFileObject('\\Some\\Fqn');
    }

    private function getCreator(): AbstractEntityFactoryCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new AbstractEntityFactoryCreator(
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

namespace My\Test\Project\Entity\Factories;

// phpcs:disable -- line length
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

// phpcs:enable

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class AbstractEntityFactory
{
    /**
     * @var DSM\Factory\EntityFactoryInterface
     */
    protected $entityFactory;

    public function __construct(
        DSM\Factory\EntityFactoryInterface $entityFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->entityFactory = $entityFactory;
        $this->entityFactory->setEntityManager($entityManager);
    }
}
';
        $actual   = $file->getContents();
        self::assertSame($expected, $actual);
    }
}
