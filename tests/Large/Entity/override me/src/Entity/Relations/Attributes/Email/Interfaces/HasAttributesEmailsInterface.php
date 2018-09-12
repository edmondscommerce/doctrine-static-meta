<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Attributes\Email\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Attributes\EmailInterface;

interface HasAttributesEmailsInterface
{
    public const PROPERTY_NAME_ATTRIBUTES_EMAILS = 'attributesEmails';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForAttributesEmails(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|EmailInterface[]
     */
    public function getAttributesEmails(): Collection;

    /**
     * @param Collection|EmailInterface[] $attributesEmails
     *
     * @return self
     */
    public function setAttributesEmails(Collection $attributesEmails): self;

    /**
     * @param EmailInterface|null $attributesEmail
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addAttributesEmail(
        ?EmailInterface $attributesEmail,
        bool $recip = true
    ): HasAttributesEmailsInterface;

    /**
     * @param EmailInterface $attributesEmail
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAttributesEmail(
        EmailInterface $attributesEmail,
        bool $recip = true
    ): HasAttributesEmailsInterface;

}
