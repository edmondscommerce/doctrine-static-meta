<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\FakerData\Financial;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class MoneyEmbeddableFakerData extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        return MoneyEmbeddable::create(
            [
                $this->generator->randomNumber(),
                $this->generator->currencyCode,
            ]
        );
    }
}
