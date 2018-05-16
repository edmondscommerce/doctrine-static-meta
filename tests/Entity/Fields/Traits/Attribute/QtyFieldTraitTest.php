<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\QtyFieldInterface; 

class QtyFieldTraitTest extends AbstractFieldTraitTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/QtyFieldTraitTest/';
    protected const TEST_FIELD_FQN =   QtyFieldTrait::class;
    protected const TEST_FIELD_PROP =  QtyFieldInterface::PROP_QTY;
}
