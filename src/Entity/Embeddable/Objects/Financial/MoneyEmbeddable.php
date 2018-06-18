<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Money\Currency;
use Money\Money;

class MoneyEmbeddable implements MoneyEmbeddableInterface
{
    /**
     * @var string
     */
    private $amount = MoneyEmbeddableInterface::DEFAULT_AMOUNT;

    /**
     * @var string
     */
    private $currencyCode = MoneyEmbeddableInterface::DEFAULT_CURRENCY_CODE;

    /**
     * @var Money
     */
    private $money;

    public function getMoney(): Money
    {
        if (null === $this->money) {
            $this->money = new Money($this->amount, new Currency($this->currencyCode));
        }

        return $this->money;
    }

    public function setMoney(Money $money): MoneyEmbeddableInterface
    {
        $this->money        = $money;
        $this->amount       = $money->getAmount();
        $this->currencyCode = $money->getCurrency()->getCode();

        return $this;
    }

    public function addMoney(Money $money): MoneyEmbeddableInterface
    {
        $this->setMoney($this->money->add($money));

        return $this;
    }

    public function subtractMoney(Money $money): MoneyEmbeddableInterface
    {
        $this->setMoney($this->money->subtract($money));

        return $this;
    }

    public static function embeddableMeta(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [
                MoneyEmbeddableInterface::EMBEDDED_PROP_CURRENCY,
                MoneyEmbeddableInterface::EMBEDDED_PROP_AMOUNT,
            ], $builder
        );
    }
}
