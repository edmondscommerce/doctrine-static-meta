<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Some\Client\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Some\ClientInterface;

interface HasSomeClientInterface
{
    public const PROPERTY_NAME_SOME_CLIENT = 'someClient';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForSomeClient(ClassMetadataBuilder $builder): void;

    /**
     * @return null|ClientInterface
     */
    public function getSomeClient(): ?ClientInterface;

    /**
     * @param ClientInterface|null $someClient
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setSomeClient(
        ?ClientInterface $someClient,
        bool $recip = true
    ): HasSomeClientInterface;

    /**
     * @param null|ClientInterface $someClient
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeSomeClient(
        ?ClientInterface $someClient = null,
        bool $recip = true
    ): HasSomeClientInterface;
}
