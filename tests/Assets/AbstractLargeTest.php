<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Assets;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverInterface;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;

abstract class AbstractLargeTest extends AbstractTest
{
    protected function setupCopiedWorkDirAndCreateDatabase(): void
    {
        $this->setupCopiedWorkDir();
        $database = $this->container->get(Database::class);
        $database->drop(true);
        $database->create(true);
        $schema = $this->container->get(Schema::class);
        $schema->create();
        $schema->validate();
    }

    /**
     * @return string
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
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
