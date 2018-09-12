<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Attributes\Email\Interfaces;

use My\Test\Project\Entity\Interfaces\Attributes\EmailInterface;

interface ReciprocatesAttributesEmailInterface
{
    /**
     * @param EmailInterface $attributesEmail
     *
     * @return self
     */
    public function reciprocateRelationOnAttributesEmail(
        EmailInterface $attributesEmail
    ): self;

    /**
     * @param EmailInterface $attributesEmail
     *
     * @return self
     */
    public function removeRelationOnAttributesEmail(
        EmailInterface $attributesEmail
    ): self;
}
