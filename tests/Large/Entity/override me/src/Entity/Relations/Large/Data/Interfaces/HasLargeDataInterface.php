<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Large\Data\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Large\DataInterface;

interface HasLargeDataInterface
{
    public const PROPERTY_NAME_LARGE_DATA = 'largeData';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForLargeData(ClassMetadataBuilder $builder): void;

    /**
     * @return null|DataInterface
     */
    public function getLargeData(): ?DataInterface;

    /**
     * @param DataInterface|null $largeData
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setLargeData(
        ?DataInterface $largeData,
        bool $recip = true
    ): HasLargeDataInterface;

    /**
     * @param null|DataInterface $largeData
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLargeData(
        ?DataInterface $largeData = null,
        bool $recip = true
    ): HasLargeDataInterface;
}
