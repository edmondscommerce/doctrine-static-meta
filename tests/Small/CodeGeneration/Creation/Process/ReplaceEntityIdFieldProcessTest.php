<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntityIdFieldProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\IdFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\NonBinaryUuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait;
use PHPUnit\Framework\TestCase;

/**
 * @small
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntityIdFieldProcess
 */
class ReplaceEntityIdFieldProcessTest extends TestCase
{
    /**
     * @test
     *
     * @param string $idTraitFqn
     *
     * @param string $shortName
     *
     * @dataProvider provideIdTraits
     */
    public function itCanReplaceTheId(string $idTraitFqn, string $shortName): void
    {
        $file = $this->getFile();
        $this->getProcess()->setIdTraitFqn($idTraitFqn)->run($this->getFindReplace($file));
        $expected = '<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

class TemplateEntity implements TemplateEntityInterface
{
    // phpcs:enable
    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidatedEntityTrait;

    use DSM\Traits\ImplementNotifyChangeTrackingPolicy;

    use DSM\Traits\AlwaysValidTrait;

    use DSM\Fields\Traits\PrimaryKey\\' . $shortName . ';

    use DSM\Traits\JsonSerializableTrait;
}
';
        $actual   = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getFile(): File
    {
        $file = new File();
        $file->setContents(
            \ts\file_get_contents(AbstractCreator::ROOT_TEMPLATE_PATH . '/src/Entities/TemplateEntity.php')
        );

        return $file;
    }

    private function getProcess(): ReplaceEntityIdFieldProcess
    {
        return new ReplaceEntityIdFieldProcess();
    }

    private function getFindReplace(File $file): File\FindReplace
    {
        return new File\FindReplace($file);
    }

    public function provideIdTraits(): array
    {
        $namespaceHelper    = $this->getNamespaceHelper();
        $idShort            = $namespaceHelper->getClassShortName(IdFieldTrait::class);
        $uuidShort          = $namespaceHelper->getClassShortName(UuidFieldTrait::class);
        $nonBinaryUuidShort = $namespaceHelper->getClassShortName(NonBinaryUuidFieldTrait::class);

        return [
            $idShort            => [IdFieldTrait::class, $idShort],
            $uuidShort          => [UuidFieldTrait::class, $uuidShort],
            $nonBinaryUuidShort => [NonBinaryUuidFieldTrait::class, $nonBinaryUuidShort],
        ];
    }

    private function getNamespaceHelper(): NamespaceHelper
    {
        return new NamespaceHelper();
    }
}
