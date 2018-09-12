<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Another\Deeply\Nested\ClientInterface;

interface HasAnotherDeeplyNestedClientInterface
{
    public const PROPERTY_NAME_ANOTHER_DEEPLY_NESTED_CLIENT = 'anotherDeeplyNestedClient';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForAnotherDeeplyNestedClient(ClassMetadataBuilder $builder): void;

    /**
     * @return null|ClientInterface
     */
    public function getAnotherDeeplyNestedClient(): ?ClientInterface;

    /**
     * @param ClientInterface|null $anotherDeeplyNestedClient
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setAnotherDeeplyNestedClient(
        ?ClientInterface $anotherDeeplyNestedClient,
        bool $recip = true
    ): HasAnotherDeeplyNestedClientInterface;

    /**
     * @param null|ClientInterface $anotherDeeplyNestedClient
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAnotherDeeplyNestedClient(
        ?ClientInterface $anotherDeeplyNestedClient = null,
        bool $recip = true
    ): HasAnotherDeeplyNestedClientInterface;
}
