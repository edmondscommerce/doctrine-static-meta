<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Some\Client\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Some\ClientInterface;

interface HasSomeClientsInterface
{
    public const PROPERTY_NAME_SOME_CLIENTS = 'someClients';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForSomeClients(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|ClientInterface[]
     */
    public function getSomeClients(): Collection;

    /**
     * @param Collection|ClientInterface[] $someClients
     *
     * @return self
     */
    public function setSomeClients(Collection $someClients): self;

    /**
     * @param ClientInterface|null $someClient
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addSomeClient(
        ?ClientInterface $someClient,
        bool $recip = true
    ): HasSomeClientsInterface;

    /**
     * @param ClientInterface $someClient
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeSomeClient(
        ClientInterface $someClient,
        bool $recip = true
    ): HasSomeClientsInterface;

}
