<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\QtyFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class QtyFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/QtyFieldTraitTest/';
    protected const TEST_FIELD_FQN     = QtyFieldTrait::class;
    protected const TEST_FIELD_PROP    = QtyFieldInterface::PROP_QTY;
    protected const TEST_FIELD_DEFAULT = QtyFieldInterface::DEFAULT_QTY;
}
