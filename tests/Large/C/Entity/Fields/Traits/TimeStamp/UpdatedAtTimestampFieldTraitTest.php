<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\TimeStamp;

use DateTimeImmutable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\TimeStamp\CreationTimestampFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\TimeStamp\UpdatedAtTimestampFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\TimeStamp\CreationTimestampFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\TimeStamp\UpdatedAtTimestampFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;
use Exception;

use function method_exists;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\TimeStamp\UpdatedAtTimestampFieldTrait
 */
class UpdatedAtTimestampFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/'
                                         . self::TEST_TYPE_LARGE . '/UpdatedAtTimestampFieldTraitTest/';
    protected const TEST_FIELD_FQN     = UpdatedAtTimestampFieldTrait::class;
    protected const TEST_FIELD_PROP    = UpdatedAtTimestampFieldInterface::PROP_UPDATED_AT_TIMESTAMP;
    protected const TEST_FIELD_DEFAULT = UpdatedAtTimestampFieldInterface::DEFAULT_UPDATED_AT_TIMESTAMP;
    protected const HAS_SETTER         = false;
    protected const VALIDATES          = false;


    public function setup():void
    {
        parent::setup();
        $this->createDatabase();
    }

    /**
     * @test
     * @throws Exception
     */
    public function createEntityWithField(): void
    {
        $entity = $this->getEntity();
        $getter = $this->getGetter($entity);
        self::assertTrue(method_exists($entity, $getter));
        $value = $entity->$getter();
        self::assertInstanceOf(DateTimeImmutable::class, $value);
    }

    /**
     * @test
     * @throws Exception
     */
    public function updateEntityWithField(): void
    {
        $entity = $this->getEntity();
        $getter = $this->getGetter($entity);
        self::assertTrue(method_exists($entity, $getter));
        $valueCreated = $entity->$getter();
        self::assertInstanceOf(DateTimeImmutable::class, $valueCreated);
        $saver = $this->getEntitySaver();
        sleep(2);
        $this->getEntityManager()->clear();
        $saver->save($entity);
        $valueUpdated = $entity->$getter();
        self::assertInstanceOf(DateTimeImmutable::class, $valueUpdated);
        self::assertGreaterThan($valueCreated, $valueUpdated);
    }
}
