<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity\HasFullNameEmbeddableTrait;

class SetEmbeddableCommandTest extends AbstractCommandIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH . '/' . self::TEST_TYPE . '/SetEmbeddableCommandTest/';


    public function testSetEmbeddable(): void
    {
        $command  = $this->container->get(SetEmbeddableCommand::class);
        $tester   = $this->getCommandTester($command);
        $entities = $this->generateEntities();

        $tester->execute(
            [
                '-' . SetEmbeddableCommand::OPT_ENTITY_SHORT     => $entities[0],
                '-' . SetEmbeddableCommand::OPT_EMBEDDABLE_SHORT => HasMoneyEmbeddableTrait::class,
            ]
        );

        $tester->execute(
            [
                '-' . SetEmbeddableCommand::OPT_ENTITY_SHORT     => $entities[1],
                '-' . SetEmbeddableCommand::OPT_EMBEDDABLE_SHORT => HasFullNameEmbeddableTrait::class,
            ]
        );

        $tester->execute(
            [
                '--' . SetEmbeddableCommand::OPT_ENTITY     => $entities[2],
                '--' . SetEmbeddableCommand::OPT_EMBEDDABLE => HasAddressEmbeddableTrait::class,
            ]
        );

        self::assertTrue($this->qaGeneratedCode());
    }
}
