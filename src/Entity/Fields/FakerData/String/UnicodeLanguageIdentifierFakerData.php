<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Faker\Generator;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Intl\Languages;

class UnicodeLanguageIdentifierFakerData extends AbstractFakerDataProvider
{
    /**
     * @var string[]
     */
    private array $languages;

    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
        $this->languages = Languages::getNames();
    }

    public function __invoke()
    {
        do {
            $language = $this->generator->languageCode;
        } while (!isset($this->languages[$language]));

        return $language;
    }
}
