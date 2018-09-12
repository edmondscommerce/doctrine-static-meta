<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Company;
use My\Test\Project\Entity\Interfaces\CompanyInterface;
// phpcs: enable
class CompanyFactory extends AbstractEntityFactory
{
    public function create(array $values = []): CompanyInterface
    {
        return $this->entityFactory->create(Company::class, $values);
    }
}
