<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Another\Deeply\Nested\ClientInterface;

interface HasAnotherDeeplyNestedClientsInterface
{
    public const PROPERTY_NAME_ANOTHER_DEEPLY_NESTED_CLIENTS = 'anotherDeeplyNestedClients';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForAnotherDeeplyNestedClients(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|ClientInterface[]
     */
    public function getAnotherDeeplyNestedClients(): Collection;

    /**
     * @param Collection|ClientInterface[] $anotherDeeplyNestedClients
     *
     * @return self
     */
    public function setAnotherDeeplyNestedClients(Collection $anotherDeeplyNestedClients): self;

    /**
     * @param ClientInterface|null $anotherDeeplyNestedClient
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addAnotherDeeplyNestedClient(
        ?ClientInterface $anotherDeeplyNestedClient,
        bool $recip = true
    ): HasAnotherDeeplyNestedClientsInterface;

    /**
     * @param ClientInterface $anotherDeeplyNestedClient
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAnotherDeeplyNestedClient(
        ClientInterface $anotherDeeplyNestedClient,
        bool $recip = true
    ): HasAnotherDeeplyNestedClientsInterface;

}
