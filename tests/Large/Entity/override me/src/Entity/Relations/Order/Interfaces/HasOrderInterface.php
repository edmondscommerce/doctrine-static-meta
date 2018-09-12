<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\OrderInterface;

interface HasOrderInterface
{
    public const PROPERTY_NAME_ORDER = 'order';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForOrder(ClassMetadataBuilder $builder): void;

    /**
     * @return null|OrderInterface
     */
    public function getOrder(): ?OrderInterface;

    /**
     * @param OrderInterface|null $order
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setOrder(
        ?OrderInterface $order,
        bool $recip = true
    ): HasOrderInterface;

    /**
     * @param null|OrderInterface $order
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrder(
        ?OrderInterface $order = null,
        bool $recip = true
    ): HasOrderInterface;
}
