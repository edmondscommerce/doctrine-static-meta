<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Faker\Generator;
use Symfony\Component\Intl\Intl;

class LocaleIdentifierFakerDataProvider extends AbstractFakerDataProvider
{

    /**
     * @var \Symfony\Component\Intl\ResourceBundle\LocaleBundleInterface
     */
    private static $localeBundle;
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
            self::$localeBundle = Intl::getLocaleBundle();
            self::$locales      = self::$localeBundle->getLocaleNames();
        }
    }

    private function isValid(string $value): bool
    {
        if (!isset(self::$locales[$value]) && !\in_array($value, self::$localeBundle->getAliases(), true)) {
            return false;
        }

        return true;
    }

    public function __invoke(): string
    {
        do {
            $value = $this->generator->locale;
        } while (false === $this->isValid($value));

        return $value;
    }
}


