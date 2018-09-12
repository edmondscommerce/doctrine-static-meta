<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Attributes\Email\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Attributes\EmailInterface;

interface HasAttributesEmailInterface
{
    public const PROPERTY_NAME_ATTRIBUTES_EMAIL = 'attributesEmail';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForAttributesEmail(ClassMetadataBuilder $builder): void;

    /**
     * @return null|EmailInterface
     */
    public function getAttributesEmail(): ?EmailInterface;

    /**
     * @param EmailInterface|null $attributesEmail
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setAttributesEmail(
        ?EmailInterface $attributesEmail,
        bool $recip = true
    ): HasAttributesEmailInterface;

    /**
     * @param null|EmailInterface $attributesEmail
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAttributesEmail(
        ?EmailInterface $attributesEmail = null,
        bool $recip = true
    ): HasAttributesEmailInterface;
}
