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
    public function itCanReplaceADeeplyNestedEntitiesNamespace(): void
    {
        $file = new File();
        $file->setContents(
            \ts\file_get_contents(AbstractCreator::ROOT_TEMPLATE_PATH . '/src/Entities/TemplateEntity.php')
        );
        $findReplace = new File\FindReplace($file);
        $findReplace->findReplace('TemplateNamespace', 'TestProject');
        $entityFqn = 'TestProject\\Entities\\Deeply\\Nested\\TemplateEntity';
        $this->getProcess()->setEntityFqn($entityFqn)->run($findReplace);
        $expected = '<?php declare(strict_types=1);

namespace TestProject\Entities\Deeply\Nested;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use TestProject\Entity\Interfaces\Deeply\Nested\TemplateEntityInterface;

class TemplateEntity implements TemplateEntityInterface
{
    // phpcs:enable
    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidatedEntityTrait;

    use DSM\Traits\ImplementNotifyChangeTrackingPolicy;

    use DSM\Traits\AlwaysValidTrait;

    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;

    use DSM\Traits\JsonSerializableTrait;
}
';
        $actual   = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getProcess(): ReplaceEntitiesSubNamespaceProcess
    {
        $process = new ReplaceEntitiesSubNamespaceProcess();
        $process->setProjectRootNamespace('TestProject');

        return $process;
    }

    /**
     * @test
     */
    public function itCanHandleTheFixtures(): void
    {
        $file      = new File();
        $entityFqn = 'TestProject\\Entities\\Deeply\\Nested\\Entities\\TestEntity';
        $file->setContents('<?php declare(strict_types=1);

namespace TestProject\Assets\Entity\Fixtures;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader;

class TestEntityFixture extends AbstractEntityFixtureLoader
{

}
');
        $this->getProcess()->setEntityFqn($entityFqn)->run(new File\FindReplace($file));
        $expected = '<?php declare(strict_types=1);

namespace TestProject\Assets\Entity\Fixtures\Deeply\Nested\Entities;

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
    public function itDiesIfNotAnEntityFqn(): void
    {
        $replaceNamespace = '\\FooBar\\Deeply\\Nested';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This does not look like an Entity');
        $this->getProcess()->setEntityFqn($replaceNamespace);
    }
}
