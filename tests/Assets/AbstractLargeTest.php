<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Assets;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverInterface;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Exception;

abstract class AbstractLargeTest extends AbstractTest
{
    public const TEST_TYPE_MEDIUM = 'Large';

    public const TEST_PROJECT_ROOT_NAMESPACE = 'My\\LargeTest\\Project';

    protected function setupCopiedWorkDirAndCreateDatabase(): void
    {
        $this->setupCopiedWorkDir();
        $database = $this->container->get(Database::class);
        $database->drop(true);
        $database->create(true);
        $schema = $this->container->get(Schema::class);
        $schema->create();
    }

    /**
     * @return string
     * @throws Exception\ConfigException
     * @throws Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    protected function setupCopiedWorkDir(): string
    {
        $copiedWorkDir = parent::setupCopiedWorkDir();
        $this->setupContainer(
            $copiedWorkDir
            . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
            . '/' . AbstractGenerator::ENTITIES_FOLDER_NAME
        );

        return $copiedWorkDir;
    }

    /**
     * @return EntitySaverInterface
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    protected function getEntitySaver(): EntitySaverInterface
    {
        return $this->container->get(EntitySaver::class);
    }
}
