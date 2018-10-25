<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Process\Src\Entity\Interfaces;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\Interfaces\AddSettableUuidInterfaceProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\FindReplace;
use PHPUnit\Framework\TestCase;
// phpcs:enable
/**
 * @small
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\Interfaces\AddSettableUuidInterfaceProcess
 */
class AddSettableUuidInterfaceProcessTest extends TestCase
{
    /**
     * @test
     */
    public function itCanAddTheInterface(): void
    {
        $file = new File();
        $file->setContents(
            \ts\file_get_contents(AbstractCreator::ROOT_TEMPLATE_PATH .
                                  '/src/Entity/Interfaces/TemplateEntityInterface.php')
        );
        $process = $this->getProcess();
        $process->run(new FindReplace($file));
        $expected = '<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\UuidPrimaryKeyInterface;

interface TemplateEntityInterface extends
    DSM\Interfaces\EntityInterface,
    UuidPrimaryKeyInterface
{

}
';
        $actual   = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getProcess(): AddSettableUuidInterfaceProcess
    {
        return new AddSettableUuidInterfaceProcess();
    }
}
