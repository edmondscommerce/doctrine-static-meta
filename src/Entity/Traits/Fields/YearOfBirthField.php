<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\Fields;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

trait YearOfBirthField
{

    /**
     * @var \DateTime
     */
    private $yearOfBirth;

    protected static function getPropertyMetaForYearOfBirth(ClassMetadataBuilder $builder)
    {
        $builder
            ->createField('yearOfBirth', Type::DATE_IMMUTABLE)
            ->nullable(true)
            ->build();
    }

    /**
     * Get yearOfBirth
     *
     * @return \DateTime
     */
    public function getYearOfBirth(): \DateTime
    {
        return $this->yearOfBirth;
    }

    /**
     * Set yearOfBirth
     *
     * @param \DateTime $yearOfBirth
     *
     * @return $this
     */
    public function setYearOfBirth(\DateTime $yearOfBirth)
    {
        $this->yearOfBirth = $yearOfBirth;

        return $this;
    }
}
