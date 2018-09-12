<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories\Company;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Company\Director;
use My\Test\Project\Entity\Interfaces\Company\DirectorInterface;
// phpcs: enable
class DirectorFactory extends AbstractEntityFactory
{
    public function create(array $values = []): DirectorInterface
    {
        return $this->entityFactory->create(Director::class, $values);
    }
}
