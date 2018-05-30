<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverInterface;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;

abstract class AbstractFunctionalTest extends AbstractIntegrationTest
{
    public const TEST_TYPE = 'functional';

    protected function setupCopiedWorkDirAndCreateDatabase()
    {
        $this->setupCopiedWorkDir();
        $database = $this->container->get(Database::class);
        $database->drop(true);
        $database->create(true);
        $schema = $this->container->get(Schema::class);
        $schema->create();
    }

    /**
     * @param string $extra
     *
     * @throws Exception\ConfigException
     * @throws Exception\DoctrineStaticMetaException
     */
    protected function setupCopiedWorkDir(string $extra = 'Copied'): void
    {
        parent::setupCopiedWorkDir($extra);
        $copiedWorkDir = rtrim(static::WORK_DIR, '/').$extra.'/';
        $this->setupContainer(
            $copiedWorkDir
            .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
            .'/'.AbstractGenerator::ENTITIES_FOLDER_NAME
        );
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
