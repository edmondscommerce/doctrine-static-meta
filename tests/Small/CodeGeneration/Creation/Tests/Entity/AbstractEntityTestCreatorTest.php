<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Tests\Entity;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\AbstractEntityTestCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\AbstractEntityTestCreator
 * @small
 */
class AbstractEntityTestCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function itCanCreateTheAbstractEntityTest()
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\AbstractEntityTest';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = '<?php declare(strict_types=1);
/**
 * To avoid collisions we use the verbose FQN for everything
 * This inevitably creates excessively long lines
 * So we disable that sniff in this file
 */
//phpcs:disable Generic.Files.LineLength.TooLong
namespace EdmondsCommerce\DoctrineStaticMeta\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

/**
 * Class AbstractEntityTest
 *
 * This abstract test is designed to give you a good level of test coverage for your entities without any work required.
 *
 * You should extend the test with methods that test your specific business logic, your validators and anything else.
 *
 * You can override the methods, properties and constants as you see fit.
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
abstract class AbstractEntityTest extends DSM\Testing\AbstractEntityTest
{
}
';
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): AbstractEntityTestCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new AbstractEntityTestCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory()
        );
    }
}