<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories\Attributes;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Attributes\Email;
use My\Test\Project\Entity\Interfaces\Attributes\EmailInterface;
// phpcs: enable
class EmailFactory extends AbstractEntityFactory
{
    public function create(array $values = []): EmailInterface
    {
        return $this->entityFactory->create(Email::class, $values);
    }
}
