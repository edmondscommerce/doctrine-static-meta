<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial;

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
        $builder->createEmbedded(HasMoneyEmbeddableInterface::PROP_MONEY_EMBEDDABLE, MoneyEmbeddable::class)
                ->setColumnPrefix(HasMoneyEmbeddableInterface::COLUMN_PREFIX_MONEY)
                ->build();
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
