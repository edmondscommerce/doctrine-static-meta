<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Faker\Generator;
use Symfony\Component\Intl\Intl;

class UnicodeLanguageIdentifierFakerData extends AbstractFakerDataProvider
{
    /**
     * @var array
     */
    private $languages;

    public function __construct(Generator $generator)
    {
        parent::__construct($generator);
        $this->languages = Intl::getLanguageBundle()->getLanguageNames();
    }

    public function __invoke()
    {
        do {
            $language = $this->generator->languageCode;
        } while (!isset($this->languages[$language]));

        return $language;
    }

}
