<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Financial;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

interface HasMoneyEmbeddableInterface extends EntityInterface
{
    public const PROP_MONEY_EMBEDDABLE = 'moneyEmbeddable';
    public const COLUMN_PREFIX_MONEY   = 'money_';

    public function getMoneyEmbeddable(): MoneyEmbeddableInterface;

    public function setMoneyEmbeddable(MoneyEmbeddableInterface $embeddable);
}
