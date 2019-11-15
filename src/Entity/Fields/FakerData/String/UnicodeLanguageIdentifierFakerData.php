<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Faker\Generator;
use RuntimeException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Intl\Languages;
use function class_exists;

class UnicodeLanguageIdentifierFakerData extends AbstractFakerDataProvider
{
    /**
     * @var array
     */
    private $languages;

    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
        $this->languages = $this->getLanguages();
    }

    public function __invoke()
    {
        do {
            $language = $this->generator->languageCode;
        } while (!isset($this->languages[$language]));

        return $language;
    }

    /**
     * Using the updated language provider if it exists
     *
     * @return array
     */
    private function getLanguages(): array
    {
        if (class_exists(Languages::class)) {
            return Languages::getNames();
        }

        if (class_exists(Intl::class)) {
            return Intl::getLanguageBundle()->getLanguageNames();
        }

        throw new RuntimeException('No language provider exists');
    }
}
