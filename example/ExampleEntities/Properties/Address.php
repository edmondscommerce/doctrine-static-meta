<?php declare(strict_types=1);


namespace Edmonds\DoctrineStaticMeta\Properties;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Edmonds\DoctrineStaticMeta\MappingHelper;
use Edmonds\DoctrineStaticMeta\Traits\Fields\IdField;
use Edmonds\DoctrineStaticMeta\Traits\Fields\LabelField;
use Edmonds\DoctrineStaticMeta\ExampleEntities\Traits\Relations\HasPeopleInversed;
use Edmonds\DoctrineStaticMeta\Traits\UsesPHPMetaData;

class Address
{
    use UsesPHPMetaData;

    use IdField,
        LabelField;

    use HasPeopleInversed;

    /**
     * @var string
     */
    private
        $firstLine,
        $secondLine,
        $thirdLine,
        $fourthLine,
        $postTown,
        $postCode,
        $country;

    /**
     * @var float
     */
    private
        $latitude,
        $longitude;


    protected static function getPropertyMetaForProperties(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleFields(
            [
                'firstLine'  => MappingHelper::TYPE_STRING,
                'secondLine' => MappingHelper::TYPE_STRING,
                'thirdLine'  => MappingHelper::TYPE_STRING,
                'fourthLine' => MappingHelper::TYPE_STRING,
                'postTown'   => MappingHelper::TYPE_STRING,
                'postCode'   => MappingHelper::TYPE_STRING,
                'country'    => MappingHelper::TYPE_STRING,
                'latitude'   => MappingHelper::TYPE_DECIMAL,
                'longitude'  => MappingHelper::TYPE_DECIMAL,
            ],
            $builder
        );
    }

    /**
     * @return string
     */
    public function getFirstLine(): string
    {
        return $this->firstLine;
    }

    /**
     * @param string $firstLine
     *
     * @return Address
     */
    public function setFirstLine(string $firstLine): Address
    {
        $this->firstLine = $firstLine;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecondLine(): string
    {
        return $this->secondLine;
    }

    /**
     * @param string $secondLine
     *
     * @return Address
     */
    public function setSecondLine(string $secondLine): Address
    {
        $this->secondLine = $secondLine;

        return $this;
    }

    /**
     * @return string
     */
    public function getThirdLine(): string
    {
        return $this->thirdLine;
    }

    /**
     * @param string $thirdLine
     *
     * @return Address
     */
    public function setThirdLine(string $thirdLine): Address
    {
        $this->thirdLine = $thirdLine;

        return $this;
    }

    /**
     * @return string
     */
    public function getFourthLine(): string
    {
        return $this->fourthLine;
    }

    /**
     * @param string $fourthLine
     *
     * @return Address
     */
    public function setFourthLine(string $fourthLine): Address
    {
        $this->fourthLine = $fourthLine;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostTown(): string
    {
        return $this->postTown;
    }

    /**
     * @param string $postTown
     *
     * @return Address
     */
    public function setPostTown(string $postTown): Address
    {
        $this->postTown = $postTown;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->postCode;
    }

    /**
     * @param string $postCode
     *
     * @return Address
     */
    public function setPostCode(string $postCode): Address
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return Address
     */
    public function setCountry(string $country): Address
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     *
     * @return Address
     */
    public function setLatitude(float $latitude): Address
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     *
     * @return Address
     */
    public function setLongitude(float $longitude): Address
    {
        $this->longitude = $longitude;

        return $this;
    }


}
