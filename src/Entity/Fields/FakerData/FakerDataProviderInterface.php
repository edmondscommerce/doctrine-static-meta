<?php declare(strict_types=1);

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
     * @return mixed
     */
    public function __invoke();
}
