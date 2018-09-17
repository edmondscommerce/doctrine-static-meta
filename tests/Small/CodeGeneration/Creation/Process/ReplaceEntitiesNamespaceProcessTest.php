<?php

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesNamespaceProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesNamespaceProcess
 * @small
 */
class ReplaceEntitiesNamespaceProcessTest extends TestCase
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
        $replaceNamespace = '\\Deeply\\Nested';
        $this->getProcess()->setEntitySubNamespace($replaceNamespace)->run(new File\FindReplace($file));
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

    private function getProcess(): ReplaceEntitiesNamespaceProcess
    {
        return new ReplaceEntitiesNamespaceProcess();
    }

    /**
     * @test
     */
    public function itDiesIfEntitySubnamespaceIncorrect()
    {
        $replaceNamespace = '\\Entities\\Deeply\\Nested';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You must not include the Entities bit at the start');
        $this->getProcess()->setEntitySubNamespace($replaceNamespace);
    }
}
