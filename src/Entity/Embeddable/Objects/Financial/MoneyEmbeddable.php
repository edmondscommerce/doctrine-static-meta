<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\AbstractEmbeddableObject;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Money\Currency;
use Money\Money;

class MoneyEmbeddable extends AbstractEmbeddableObject implements MoneyEmbeddableInterface
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
        $this->setMoney($this->getMoney()->add($money));

        return $this;
    }

    public function subtractMoney(Money $money): MoneyEmbeddableInterface
    {
        $this->setMoney($this->getMoney()->subtract($money));

        return $this;
    }

    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $builder = self::setEmbeddableAndGetBuilder($metadata);
        MappingHelper::setSimpleFields(
            [
                MoneyEmbeddableInterface::EMBEDDED_PROP_CURRENCY_CODE => MappingHelper::TYPE_STRING,
                MoneyEmbeddableInterface::EMBEDDED_PROP_AMOUNT        => MappingHelper::TYPE_INTEGER,
            ],
            $builder
        );
    }
}
