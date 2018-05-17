<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\TimestampFieldInterface;

class TimestampFieldTraitTest extends AbstractFieldTraitTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/TimestampFieldTraitTest/';
    protected const TEST_FIELD_FQN =   TimestampFieldTrait::class;
    protected const TEST_FIELD_PROP =  TimestampFieldInterface::PROP_TIMESTAMP;
}
