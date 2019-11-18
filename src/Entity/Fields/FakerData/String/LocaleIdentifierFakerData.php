<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Faker\Generator;
use RuntimeException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Intl\Locales;

use function array_flip;
use function class_exists;

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
     */
    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
        if (null === self::$locales) {
            self::$locales = $this->getLocales();
        }
    }

    /**
     * Symfony 4.3 deprecated the Intl::getLocaleBundle in favour of the new Locales class. We need to support both the
     * older projects that are using symfony 4.0 - 2 as well as newer versions so we will use the method to try and get
     * the locales the new way if possible, and then fall back to the older way if not
     *
     * @return array
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function getLocales(): array
    {
        if (class_exists(Locales::class)) {
            return array_flip(Locales::getLocales());
        }

        if (class_exists(Intl::class)) {
            return Intl::getLocaleBundle()->getLocales();
        }

        throw new RuntimeException('No locale provider exists');
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
