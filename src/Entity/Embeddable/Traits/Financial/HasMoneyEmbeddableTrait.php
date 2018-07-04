<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial;

// phpcs:disable
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Financial\HasMoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;

// phpcs:enable
trait HasMoneyEmbeddableTrait
{
    /**
     * @var MoneyEmbeddableInterface
     */
    private $moneyEmbeddable;

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForMoney(ClassMetadataBuilder $builder): void
    {
        $builder->createEmbedded(
            HasMoneyEmbeddableInterface::PROP_MONEY_EMBEDDABLE,
            MoneyEmbeddable::class
        )
                ->setColumnPrefix(
                    HasMoneyEmbeddableInterface::COLUMN_PREFIX_MONEY
                )
                ->build();
    }

    /**
     * Called at construction time
     */
    private function initMoney(): void
    {
        $this->moneyEmbeddable = new MoneyEmbeddable();
    }

    /**
     * @return MoneyEmbeddableInterface
     */
    public function getMoneyEmbeddable(): MoneyEmbeddableInterface
    {
        return $this->moneyEmbeddable;
    }

    /**
     * @param MoneyEmbeddableInterface $moneyEmbeddable
     *
     * @return $this
     */
    public function setMoneyEmbeddable(MoneyEmbeddableInterface $moneyEmbeddable): self
    {
        $this->moneyEmbeddable = $moneyEmbeddable;

        return $this;
    }
}
