<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\F\CodeGeneration\PostProcessor;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\FileOverrider;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use RuntimeException;
use function dirname;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\FileOverrider
 */
class FileOverriderTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/FileOverriderTest';

    public const TEST_FILE_RELATIVE_PATH = '/src/Entity/Factories/Another/Deeply/Nested/ClientFactory.php';
    public const TEST_FILE               = self::WORK_DIR . self::TEST_FILE_RELATIVE_PATH;

    protected static $buildOnce = true;
    /**
     * @var FileOverrider
     */
    private $overrider;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            if (!is_dir(self::WORK_DIR . '/' . FileOverrider::OVERRIDES_PATH)) {
                mkdir(self::WORK_DIR . '/' . FileOverrider::OVERRIDES_PATH, 0777, true);
            }
            self::$built = true;
        }
        $this->overrider = new FileOverrider(self::WORK_DIR);
    }

    /**
     * @test
     */
    public function itExceptsIfOverridesNotInProject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new FileOverrider(__DIR__, realpath(__DIR__ . '/../'));
    }

    /**
     * @test
     * @large
     */
    public function itCanCreateANewOverrideFile(): string
    {
        $pathToFileInProject = self::TEST_FILE;
        $overridePath        = realpath(self::WORK_DIR . $this->overrider->createNewOverride($pathToFileInProject));
        self::assertFileEquals($pathToFileInProject, $overridePath);
        $expectedOVerridePathDir = realpath(
            self::WORK_DIR . '/' . FileOverrider::OVERRIDES_PATH . '/src/Entity/Factories/Another/Deeply/Nested/'
        );
        self::assertSame($expectedOVerridePathDir, dirname($overridePath));

        return $overridePath;
    }

    /**
     * @test
     * @large
     * @depends itCanCreateANewOverrideFile
     *
     * @param string $overridePath
     *
     * @return string
     */
    public function updatedOverrideCanBeApplied(string $overridePath): string
    {
        $updatedContents = <<<'PHP'
<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories\Another\Deeply\Nested;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Another\Deeply\Nested\Client;
use My\Test\Project\Entity\Interfaces\Another\Deeply\Nested\ClientInterface;
// phpcs: enable
class ClientFactory extends AbstractEntityFactory
{
    public function create(array $values = []): ClientInterface
    {
        $client=new Client();
        $this->entityFactory->doStuff($client);
        return $client;
    }
}
PHP;
        \ts\file_put_contents($overridePath, $updatedContents);
        $this->overrider->applyOverrides();
        self::assertSame($updatedContents, \ts\file_get_contents(self::TEST_FILE));

        return $overridePath;
    }


    /**
     * @test
     * @large
     * @depends updatedOverrideCanBeApplied
     *
     * @param string $overridePath
     *
     * @return string
     */
    public function updatedProjectFileCanBeSetToOverrides(string $overridePath): string
    {
        $updatedContents = <<<'PHP'
<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories\Another\Deeply\Nested;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Another\Deeply\Nested\Client;
use My\Test\Project\Entity\Interfaces\Another\Deeply\Nested\ClientInterface;
// phpcs: enable
class ClientFactory extends AbstractEntityFactory
{
    public function create(array $values = []): ClientInterface
    {
        $client=new Client();
        $this->entityFactory->doStuff($client);
        $this->entityFactory->doMoreStuff($client);
        return $client;
    }
}
PHP;
        \ts\file_put_contents(self::TEST_FILE, $updatedContents);
        $this->overrider->updateOverrideFiles([self::TEST_FILE_RELATIVE_PATH => true]);
        self::assertSame($updatedContents, \ts\file_get_contents($overridePath));

        return $overridePath;
    }

    /**
     * @test
     * @large
     * @depends updatedProjectFileCanBeSetToOverrides
     */
    public function itPreventsYouFromCreatingDuplicateOverrides(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Override already exists for path /src/Entity/Factories/Another/Deeply/Nested/ClientFactory.php'
        );
        $this->overrider->createNewOverride(self::TEST_FILE);
    }

    /**
     * @test
     * @large
     * @depends updatedProjectFileCanBeSetToOverrides
     */
    public function overridesCanNotBeAppliedIfTheProjectFileHashDoesNotMatch(): void
    {
        $updatedContents = <<<'PHP'
<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories\Another\Deeply\Nested;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Another\Deeply\Nested\Client;
use My\Test\Project\Entity\Interfaces\Another\Deeply\Nested\ClientInterface;
// phpcs: enable
class ClientFactory extends AbstractEntityFactory
{
    private function somethingNewlyGenerated(){
        return 'this represents something new in the generated code that will mean the hash wont work';
    }

    public function create(array $values = []): ClientInterface
    {
        $client=new Client();
        return $client;
    }
}
PHP;
        \ts\file_put_contents(self::TEST_FILE, $updatedContents);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'These file hashes were not up to date:'
        );
        $this->overrider->applyOverrides();
    }
}
