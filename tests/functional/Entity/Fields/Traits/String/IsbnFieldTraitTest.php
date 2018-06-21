<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IsbnFieldInterface;

class IsbnFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/IsbnFieldTraitTest/';
    protected const TEST_FIELD_FQN =   IsbnFieldTrait::class;
    protected const TEST_FIELD_PROP =  IsbnFieldInterface::PROP_ISBN;
    protected const TEST_FIELD_DEFAULT = IsbnFieldInterface::DEFAULT_ISBN;
}
