<?php

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesSubNamespaceProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesSubNamespaceProcess
 * @small
 */
class ReplaceEntitiesSubNamespaceProcessTest extends TestCase
{
    /**
     * @test
     */
    public function itCanReplaceADeeplyNestedEntitiesNamespace()
    {
        $file = new File();
        $file->setContents(
            \ts\file_get_contents(AbstractCreator::ROOT_TEMPLATE_PATH . '/src/Entities/TemplateEntity.php')
        );
        $entityFqn = 'TemplateNamespace\Entities\Deeply\Nested\TemplateEntity';
        $this->getProcess()->setEntityFqn($entityFqn)->run(new File\FindReplace($file));
        $expected = '<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Deeply\Nested;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use TemplateNamespace\Entity\Interfaces\Deeply\Nested\TemplateEntityInterface;

class TemplateEntity implements TemplateEntityInterface
{
    // phpcs:enable
    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidatedEntityTrait;

    use DSM\Traits\ImplementNotifyChangeTrackingPolicy;

    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;


    public function __construct()
    {
        $this->runInitMethods();
    }
}
';
        $actual   = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getProcess(): ReplaceEntitiesSubNamespaceProcess
    {
        return new ReplaceEntitiesSubNamespaceProcess();
    }

    /**
     * @test
     */
    public function itCanHandleTheFixtures()
    {
        $file      = new File();
        $entityFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\Deeply\\Nested\\Entities\\TestEntity';
        $file->setContents('<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Assets\Entity\Fixtures;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader;

class TestEntityFixture extends AbstractEntityFixtureLoader
{

}
');
        $this->getProcess()->setEntityFqn($entityFqn)->run(new File\FindReplace($file));
        $expected = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Assets\Entity\Fixtures\Deeply\Nested\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader;

class TestEntityFixture extends AbstractEntityFixtureLoader
{

}
';
        $actual   = $file->getContents();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itDiesIfNotAnEntityFqn()
    {
        $replaceNamespace = '\\FooBar\\Deeply\\Nested';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This does not look like an Entity');
        $this->getProcess()->setEntityFqn($replaceNamespace);
    }
}
