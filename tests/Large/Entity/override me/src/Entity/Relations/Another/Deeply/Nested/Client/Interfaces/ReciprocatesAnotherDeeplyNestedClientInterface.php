<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Interfaces;

use My\Test\Project\Entity\Interfaces\Another\Deeply\Nested\ClientInterface;

interface ReciprocatesAnotherDeeplyNestedClientInterface
{
    /**
     * @param ClientInterface $anotherDeeplyNestedClient
     *
     * @return self
     */
    public function reciprocateRelationOnAnotherDeeplyNestedClient(
        ClientInterface $anotherDeeplyNestedClient
    ): self;

    /**
     * @param ClientInterface $anotherDeeplyNestedClient
     *
     * @return self
     */
    public function removeRelationOnAnotherDeeplyNestedClient(
        ClientInterface $anotherDeeplyNestedClient
    ): self;
}
