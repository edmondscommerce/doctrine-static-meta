<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\E\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateDtosForAllEntitiesAction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DtoCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateDtosForAllEntitiesAction
 * @large
 */
class CreateDataTransferObjectsForAllEntitiesActionTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_LARGE .
                            '/CreateDataTransferObjectsForAllEntitiesActionTest';

    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            $this->getFileSystem()->remove(self::WORK_DIR . '/src/Entity/DataTransferObjects');
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
    }

    /**
     * @test
     */
    public function itCanCreateDtosForAllEntities(): void
    {
        $this->getAction()->run();
        self::assertFileExists($this->copiedWorkDir . '/src/Entity/DataTransferObjects/PersonDto.php');
        $this->qaGeneratedCode();
    }

    private function getAction(): CreateDtosForAllEntitiesAction
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        $creator = new DtoCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory(),
            new ReflectionHelper($namespaceHelper),
            new CodeHelper($namespaceHelper)
        );

        $action = new CreateDtosForAllEntitiesAction($creator, $namespaceHelper);
        $action->setProjectRootNamespace($this->copiedRootNamespace);
        $action->setProjectRootDirectory($this->copiedWorkDir);

        return $action;
    }

    /**
     * @test
     */
    public function itCanBeRunMultipleTimes(): void
    {
        $this->getAction()->run();
        $this->getAction()->run();
        self::assertFileExists($this->copiedWorkDir . '/src/Entity/DataTransferObjects/PersonDto.php');
    }
}
