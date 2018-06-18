<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Financial;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;

interface HasMoneyEmbeddableInterface
{
    public function getMoneyEmbeddable(): MoneyEmbeddableInterface;

    public function setMoneyEmbeddable(MoneyEmbeddableInterface $embeddable): HasMoneyEmbeddableInterface;
}
