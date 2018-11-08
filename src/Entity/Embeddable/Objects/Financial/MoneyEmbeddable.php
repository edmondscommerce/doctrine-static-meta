<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Financial\HasMoneyEmbeddableInterface;
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

    public function __construct(Money $money)
    {
        $this->setMoney($money);
    }

    private function setMoney(Money $money): MoneyEmbeddableInterface
    {
        $amount = $money->getAmount();
        $this->notifyEmbeddablePrefixedProperties(
            self::EMBEDDED_PROP_AMOUNT,
            $this->amount,
            $amount
        );
        $currencyCode = $money->getCurrency()->getCode();
        $this->notifyEmbeddablePrefixedProperties(
            self::EMBEDDED_PROP_CURRENCY_CODE,
            $this->currencyCode,
            $currencyCode
        );
        $this->money        = $money;
        $this->amount       = $amount;
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * @param ClassMetadata $metadata
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $builder = self::setEmbeddableAndGetBuilder($metadata);
        MappingHelper::setSimpleFields(
            [
                MoneyEmbeddableInterface::EMBEDDED_PROP_CURRENCY_CODE => MappingHelper::TYPE_STRING,
            ],
            $builder
        );
        //Using BIGINT to ensure we can store very (very) large sums of cash
        $builder->createField(MoneyEmbeddableInterface::EMBEDDED_PROP_AMOUNT, Type::BIGINT)
                ->columnName(
                    MappingHelper::getColumnNameForField(
                        MoneyEmbeddableInterface::EMBEDDED_PROP_AMOUNT
                    )
                )
                ->nullable(true)
                ->build();
    }

    /**
     * @param array $properties
     *
     * @return MoneyEmbeddableInterface
     */
    public static function create(array $properties): MoneyEmbeddableInterface
    {
        if (array_key_exists(MoneyEmbeddableInterface::EMBEDDED_PROP_AMOUNT, $properties)) {
            return new self(
                new Money(
                    $properties[MoneyEmbeddableInterface::EMBEDDED_PROP_AMOUNT],
                    new Currency($properties[MoneyEmbeddableInterface::EMBEDDED_PROP_CURRENCY_CODE])
                )
            );
        }
        list($amount, $currency) = array_values($properties);
        $money = new Money($amount, new Currency($currency));

        return new self($money);
    }

    public function __toString(): string
    {
        return (string)print_r(
            [
                'moneyEmbeddable' => [
                    'amount'   => $this->getMoney()->getAmount(),
                    'currency' => $this->getMoney()->getCurrency(),
                ],
            ],
            true
        );
    }

    public function getMoney(): Money
    {
        if (null === $this->money) {
            $this->money = new Money($this->amount, new Currency($this->currencyCode));
        }

        return $this->money;
    }

    protected function getPrefix(): string
    {
        return HasMoneyEmbeddableInterface::PROP_MONEY_EMBEDDABLE;
    }
}
