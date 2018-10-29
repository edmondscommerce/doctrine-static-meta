<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\FakerData\Financial;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Money\Currency;
use Money\Money;

class MoneyEmbeddableFakerData extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        $embeddable = new MoneyEmbeddable();
        $money      = new Money($this->generator->randomNumber(), new Currency($this->generator->currencyCode));
        $embeddable->setMoney($money);

        return $embeddable;
    }
}
