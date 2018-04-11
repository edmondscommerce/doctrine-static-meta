<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Person;

interface YearOfBirthFieldInterface
{
    public const PROP_YEAR_OF_BIRTH = 'yearOfBirth';

    public function getYearOfBirth(): \DateTime;

    public function setYearOfBirth(\DateTime $yearOfBirth);
}
