<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Assets;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixturesHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixturesHelperFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractLargeTest extends AbstractTest
{
    protected function setupCopiedWorkDirAndCreateDatabase(): void
    {
        $this->setupCopiedWorkDir();
        $this->createDatabase();
    }

    protected function createDatabase(): void
    {
        $database = $this->container->get(Database::class);
        $database->drop(true);
        $database->create(true);
        $schema = $this->container->get(Schema::class);
        $schema->create();
        $schema->validate();
    }

    /**
     * @return EntitySaverInterface
     * @throws DoctrineStaticMetaException
     */
    protected function getEntitySaver(): EntitySaverInterface
    {
        return $this->container->get(EntitySaver::class);
    }

    protected function getFixturesHelper(): FixturesHelper
    {
        return $this->container->get(FixturesHelperFactory::class)->getFixturesHelper();
    }
}
