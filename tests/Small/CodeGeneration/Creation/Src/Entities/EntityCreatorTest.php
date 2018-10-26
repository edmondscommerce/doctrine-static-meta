<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entities;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntityIdFieldProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entities\EntityCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entities\EntityCreator
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator
 */
class EntityCreatorTest extends TestCase
{
    /**
     * @test
     * @small
     */
    public function itCanCreateANewEntity(): void
    {
        $newObjectFqn = 'Test\\Project\\Entities\\TestEntity';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = '<?php declare(strict_types=1);

namespace Test\Project\Entities;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use Test\Project\Entity\Interfaces\TestEntityInterface;

class TestEntity implements TestEntityInterface
{
    // phpcs:enable
    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidatedEntityTrait;

    use DSM\Traits\ImplementNotifyChangeTrackingPolicy;

    use DSM\Traits\AlwaysValidTrait;

    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;
}
';
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): EntityCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        $creator = new EntityCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory()
        );
        $creator->setProjectRootNamespace('Test\Project');

        return $creator;
    }

    /**
     * @test
     * @small
     */
    public function itCanCreateADeeplyNamespaceNewEntity(): void
    {
        $newObjectFqn = 'Test\\Project\\Entities\\Deeply\\Namespaced\\TestEntity';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = '<?php declare(strict_types=1);

namespace Test\Project\Entities\Deeply\Namespaced;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use Test\Project\Entity\Interfaces\Deeply\Namespaced\TestEntityInterface;

class TestEntity implements TestEntityInterface
{
    // phpcs:enable
    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidatedEntityTrait;

    use DSM\Traits\ImplementNotifyChangeTrackingPolicy;

    use DSM\Traits\AlwaysValidTrait;

    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;
}
';
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     */
    public function itCanSpecifyTheIdFieldTrait(): void
    {
        $newObjectFqn = 'Test\\Project\\Entities\\Deeply\\Namespaced\\TestEntity';
        $creator      = $this->getCreator();
        $creator->setReplaceIdFieldProcess(
            (new ReplaceEntityIdFieldProcess())->setIdTraitFqn(UuidFieldTrait::class)
        );
        $file     = $creator->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected = '<?php declare(strict_types=1);

namespace Test\Project\Entities\Deeply\Namespaced;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use Test\Project\Entity\Interfaces\Deeply\Namespaced\TestEntityInterface;

class TestEntity implements TestEntityInterface
{
    // phpcs:enable
    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidatedEntityTrait;

    use DSM\Traits\ImplementNotifyChangeTrackingPolicy;

    use DSM\Traits\AlwaysValidTrait;

    use DSM\Fields\Traits\PrimaryKey\UuidFieldTrait;
}
';
        $actual   = $file->getContents();
        self::assertSame($expected, $actual);
    }
}
