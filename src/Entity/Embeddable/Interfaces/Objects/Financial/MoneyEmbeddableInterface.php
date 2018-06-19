<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\AbstractEmbeddableObjectInterface;
use Money\Money;

interface MoneyEmbeddableInterface extends AbstractEmbeddableObjectInterface
{

    public const EMBEDDED_PROP_AMOUNT        = 'amount';
    public const EMBEDDED_PROP_CURRENCY_CODE = 'currencyCode';

    public const DEFAULT_AMOUNT = '0';
    /**
     * @see /vendor/moneyphp/money/resources/currency.php for full list
     */
    public const DEFAULT_CURRENCY_CODE = 'GBP';

    public function getMoney(): Money;

    public function setMoney(Money $money): MoneyEmbeddableInterface;

    public function addMoney(Money $money): MoneyEmbeddableInterface;

    public function subtractMoney(Money $money): MoneyEmbeddableInterface;
}
