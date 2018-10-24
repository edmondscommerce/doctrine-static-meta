<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\AbstractAnonymousUuidDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\ShortIndexedRequiredStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\ShortIndexedRequiredStringFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitTest;
use Ramsey\Uuid\UuidInterface;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\ShortIndexedRequiredStringFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\ShortIndexedRequiredStringFakerData#
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
            new class($this->getEntityFqn(), $this->getUuid()) extends AbstractAnonymousUuidDto
            {
                public function getShortIndexedRequiredString(): string
                {
                    return 'blah blah blah';
                }
            }
        );
    }

    private function getUuid(): UuidInterface
    {
        $factory = $this->container->get(UuidFactory::class);

        return $factory->getOrderedTimeUuid();
    }
}
