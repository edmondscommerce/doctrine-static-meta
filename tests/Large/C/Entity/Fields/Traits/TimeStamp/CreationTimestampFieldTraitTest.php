<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\TimeStamp;

use DateTimeImmutable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\TimeStamp\CreationTimestampFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\TimeStamp\CreationTimestampFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;
use Exception;

use function method_exists;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\TimeStamp\CreationTimestampFieldTrait
 */
class CreationTimestampFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/'
                                         . self::TEST_TYPE_LARGE . '/CreationTimestampFieldTraitTest/';
    protected const TEST_FIELD_FQN     = CreationTimestampFieldTrait::class;
    protected const TEST_FIELD_PROP    = CreationTimestampFieldInterface::PROP_CREATION_TIMESTAMP;
    protected const TEST_FIELD_DEFAULT = CreationTimestampFieldInterface::DEFAULT_CREATION_TIMESTAMP;
    protected const HAS_SETTER         = false;
    protected const VALIDATES          = false;

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
}
