<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\AbstractEntityCreationUuidDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\ShortIndexedRequiredStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\ShortIndexedRequiredStringFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\ShortIndexedRequiredStringFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\ShortIndexedRequiredStringFakerData
 */
class ShortIndexedRequiredStringFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE
                                         . '/ShortIndexedRequiredStringFieldTraitTest/';
    protected const TEST_FIELD_FQN     = ShortIndexedRequiredStringFieldTrait::class;
    protected const TEST_FIELD_PROP    = ShortIndexedRequiredStringFieldInterface::PROP_SHORT_INDEXED_REQUIRED_STRING;
    protected const TEST_FIELD_DEFAULT = 'blah blah blah';
    protected const VALID_VALUES       = [
        'something',
    ];
    protected const INVALID_VALUES     = [
        '',
    ];

    protected function getEntity()
    {
        return $this->createEntity(
            $this->getEntityFqn(),
            new class(
                $this->getEntityFqn(),
                $this->container->get(UuidFactory::class)
            ) extends AbstractEntityCreationUuidDto
            {
                public function getShortIndexedRequiredString(): string
                {
                    return 'blah blah blah';
                }
            }
        );
    }
}
