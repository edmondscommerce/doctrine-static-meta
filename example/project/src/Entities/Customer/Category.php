<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Customer;

// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
use My\Test\Project\Entity\Interfaces\Customer\CategoryInterface;
use My\Test\Project\Entity\Relations\Customer\Interfaces\HasCustomersInterface;
use My\Test\Project\Entity\Relations\Customer\Interfaces\ReciprocatesCustomerInterface;
use My\Test\Project\Entity\Relations\Customer\Traits\HasCustomers\HasCustomersInverseManyToMany;

// phpcs:enable
class Category implements
    CategoryInterface,
    HasCustomersInterface,
    ReciprocatesCustomerInterface
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\ValidatedEntityTrait;
    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;
    use HasCustomersInverseManyToMany;

    public function __construct(EntityValidatorInterface $validator)
    {
        $this->setValidator($validator);
        $this->runInitMethods();
    }
}
