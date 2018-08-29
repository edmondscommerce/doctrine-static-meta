<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Faker\Generator;
use Symfony\Component\Intl\Intl;

class LocaleIdentifierFakerData extends AbstractFakerDataProvider
{

    /**
     * @var string[]
     */
    private static $locales;

    /**
     * LocaleIdentifierFakerDataProvider constructor.
     *
     * @param Generator $generator
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
        if (null === self::$locales) {
            self::$locales = Intl::getLocaleBundle()->getLocaleNames();
        }
    }

    public function __invoke(): string
    {
        do {
            $value = $this->generator->locale;
        } while (false === $this->isValid($value));

        return $value;
    }

    private function isValid(string $value): bool
    {
        if (!isset(self::$locales[$value])) {
            return false;
        }

        return true;
    }
}
