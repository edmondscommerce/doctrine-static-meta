<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData;

use Faker\Generator;

interface FakerDataProviderInterface
{
    public function __construct(Generator $generator);

    /**
     * This magic method means that the object is callable like a closure,
     * and when that happens this invoke method is called.
     *
     * This method should return your fake data. You can use the generator to pull fake data from if that is useful
     *
     * The method is passed the EntityInterface that is being updated, however it is not defined in the interface as it
     * is not always needed in your provider implementation
     *
     * @return mixed
     */
    public function __invoke();
}
