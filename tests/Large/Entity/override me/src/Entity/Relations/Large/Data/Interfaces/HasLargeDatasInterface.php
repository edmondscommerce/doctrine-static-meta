<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Large\Data\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Large\DataInterface;

interface HasLargeDatasInterface
{
    public const PROPERTY_NAME_LARGE_DATAS = 'largeDatas';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForLargeDatas(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|DataInterface[]
     */
    public function getLargeDatas(): Collection;

    /**
     * @param Collection|DataInterface[] $largeDatas
     *
     * @return self
     */
    public function setLargeDatas(Collection $largeDatas): self;

    /**
     * @param DataInterface|null $largeData
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addLargeData(
        ?DataInterface $largeData,
        bool $recip = true
    ): HasLargeDatasInterface;

    /**
     * @param DataInterface $largeData
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLargeData(
        DataInterface $largeData,
        bool $recip = true
    ): HasLargeDatasInterface;

}
