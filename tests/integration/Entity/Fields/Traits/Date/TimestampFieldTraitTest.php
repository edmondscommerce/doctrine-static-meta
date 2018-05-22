<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\TimestampFieldInterface;

class TimestampFieldTraitTest extends AbstractFieldTraitIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/TimestampFieldTraitTest/';
    protected const TEST_FIELD_FQN =   TimestampFieldTrait::class;
    protected const TEST_FIELD_PROP =  TimestampFieldInterface::PROP_TIMESTAMP;
}
