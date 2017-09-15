<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\ExampleEntities\Traits\Relations;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\ExampleEntities\Person;

trait HasPeopleInversed
{
    use HasPeople;

    protected static function getPropertyMetaForPeople(ClassMetadataBuilder $builder)
    {
        $builder->addInverseManyToMany(
            Person::getPlural(),
            Person::class,
            static::getPlural()
        );
    }
}
