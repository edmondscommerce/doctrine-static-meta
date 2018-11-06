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
        $builder->addLifecycleEvent(
            'postLoadSetOwningEntityOnMoneyEmbeddable',
            Events::postLoad
        );
        $builder->createEmbedded(
            HasMoneyEmbeddableInterface::PROP_MONEY_EMBEDDABLE,
            MoneyEmbeddable::class
        )
                ->setColumnPrefix(
                    HasMoneyEmbeddableInterface::COLUMN_PREFIX_MONEY
                )
                ->build();
    }

    public function postLoadSetOwningEntityOnMoneyEmbeddable(): void
    {
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
     * Called at construction time
     */
    private function initMoneyEmbeddable(): void
    {
        $this->setMoneyEmbeddable(
            MoneyEmbeddable::create(MoneyEmbeddable::DEFAULTS),
            false
        );
    }

    /**
     * @param MoneyEmbeddableInterface $moneyEmbeddable
     *
     * @param bool                     $notify
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function setMoneyEmbeddable(
        MoneyEmbeddableInterface $moneyEmbeddable,
        bool $notify = true
    ): self {
        $this->moneyEmbeddable = $moneyEmbeddable;
        $this->moneyEmbeddable->setOwningEntity($this);
        if (true === $notify) {
            $this->notifyEmbeddablePrefixedProperties(
                HasMoneyEmbeddableInterface::PROP_MONEY_EMBEDDABLE
            );
        }

        return $this;
    }
}
