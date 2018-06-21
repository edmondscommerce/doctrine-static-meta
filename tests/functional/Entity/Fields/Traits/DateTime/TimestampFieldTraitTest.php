<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\CreationTimestampFieldInterface;

class TimestampFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/TimestampFieldTraitTest/';
    protected const TEST_FIELD_FQN =   CreationTimestampFieldTrait::class;
    protected const TEST_FIELD_PROP =  CreationTimestampFieldInterface::PROP_TIMESTAMP;
}
