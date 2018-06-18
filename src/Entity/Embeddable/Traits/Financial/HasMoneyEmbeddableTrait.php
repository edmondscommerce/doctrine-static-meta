<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Financial\HasMoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;

trait HasMoneyEmbeddableTrait
{
    /**
     * @var MoneyEmbeddable
     */
    private $moneyEmbeddable;

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForMoney(ClassMetadataBuilder $builder): void
    {
        $builder->createEmbedded(MoneyEmbeddableInterface::PROP_MONEY, MoneyEmbeddable::class)
                ->setColumnPrefix(MoneyEmbeddableInterface::COLUMN_PREFIX)
                ->build();
        MoneyEmbeddable::embeddableMeta($builder);
    }

    private function initMoney()
    {
        $this->moneyEmbeddable = new MoneyEmbeddable();
    }

    public function getMoneyEmbeddable(): MoneyEmbeddableInterface
    {
        return $this->moneyEmbeddable;
    }

    public function setMoneyEmbeddable(MoneyEmbeddableInterface $moneyEmbeddable): HasMoneyEmbeddableInterface
    {
        $this->moneyEmbeddable = $moneyEmbeddable;

        return $this;
    }


}
