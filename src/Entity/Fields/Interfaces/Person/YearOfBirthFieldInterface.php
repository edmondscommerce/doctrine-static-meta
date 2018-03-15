<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Person;

interface YearOfBirthFieldInterface
{
    public const PROPERTY_NAME = 'yearOfBirth';

    public function getYearOfBirth(): \DateTime;

    public function setYearOfBirth(\DateTime $yearOfBirth): YearOfBirthFieldInterface;
}