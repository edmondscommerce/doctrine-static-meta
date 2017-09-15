<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Properties;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\ExampleEntities\Traits\Relations\HasPeopleInversed;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Traits\Fields\IdField;
use EdmondsCommerce\DoctrineStaticMeta\Traits\Fields\LabelField;
use EdmondsCommerce\DoctrineStaticMeta\Traits\UsesPHPMetaData;


class PhoneNumber
{
    use UsesPHPMetaData;

    use IdField,
        LabelField;

    use HasPeopleInversed;

    /**
     * @var string
     */
    private
        $countryCode,
        $areaCode,
        $number;

    protected static function getPropertyMetaForProperties(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleFields(
            [
                'countryCode' => MappingHelper::TYPE_STRING,
                'areaCode'    => MappingHelper::TYPE_STRING,
                'number'      => MappingHelper::TYPE_STRING,
            ],
            $builder
        );
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     *
     * @return PhoneNumber
     */
    public function setCountryCode(string $countryCode): PhoneNumber
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAreaCode(): string
    {
        return $this->areaCode;
    }

    /**
     * @param string $areaCode
     *
     * @return PhoneNumber
     */
    public function setAreaCode(string $areaCode): PhoneNumber
    {
        $this->areaCode = $areaCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return PhoneNumber
     */
    public function setNumber(string $number): PhoneNumber
    {
        $this->number = $number;

        return $this;
    }

    public function __toString(): string
    {
        return $this->countryCode.' '.$this->areaCode.' '.$this->number;
    }

}
