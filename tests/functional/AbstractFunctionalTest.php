<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;

class AbstractFunctionalTest extends AbstractIntegrationTest
{
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
}
