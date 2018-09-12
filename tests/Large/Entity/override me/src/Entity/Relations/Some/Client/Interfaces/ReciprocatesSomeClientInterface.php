<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Some\Client\Interfaces;

use My\Test\Project\Entity\Interfaces\Some\ClientInterface;

interface ReciprocatesSomeClientInterface
{
    /**
     * @param ClientInterface $someClient
     *
     * @return self
     */
    public function reciprocateRelationOnSomeClient(
        ClientInterface $someClient
    ): self;

    /**
     * @param ClientInterface $someClient
     *
     * @return self
     */
    public function removeRelationOnSomeClient(
        ClientInterface $someClient
    ): self;
}
