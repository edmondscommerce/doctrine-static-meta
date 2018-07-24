<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Financial\HasMoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;

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
        $builder->addLifecycleEvent('postLoadSetOwningEntity', Events::postLoad);
        $builder->createEmbedded(
            HasMoneyEmbeddableInterface::PROP_MONEY_EMBEDDABLE,
            MoneyEmbeddable::class
        )
                ->setColumnPrefix(
                    HasMoneyEmbeddableInterface::COLUMN_PREFIX_MONEY
                )
                ->build();
    }

    public function postLoadSetOwningEntity(): void
    {
        $this->moneyEmbeddable->setOwningEntity($this);
    }

    /**
     * Called at construction time
     */
    private function initEmbeddableMoney(): void
    {
        $this->moneyEmbeddable = new MoneyEmbeddable();
        $this->moneyEmbeddable->setOwningEntity($this);
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
