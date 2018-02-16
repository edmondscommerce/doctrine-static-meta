<?php declare(strict_types=1);

namespace TemplateNamespace\EntityRepositories;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaDataTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractEntityRepository extends EntityRepository
{

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->validator = $validator;
    }

    /**
     * Get a new instance of the Entity
     *
     * @return UsesPHPMetaDataTrait
     */
    public function create(): UsesPHPMetaDataTrait
    {
        $class  = $this->getEntityName();
        $entity = new $class();
        $entity->setValidator($this->validator);

        return $entity;
    }


    /**
     * @param mixed $id
     * @param null  $lockMode
     * @param null  $lockVersion
     *
     * @return UsesPHPMetaDataTrait|null
     */
    public function find($id, $lockMode = null, $lockVersion = null): ?UsesPHPMetaDataTrait
    {
        $entity = parent::find($id, $lockMode, $lockVersion);
        if (null === $entity) {
            return null;
        }
        $entity->setValidator($this->validator);

        return $entity;
    }

    /**
     * @return array|UsesPHPMetaDataTrait[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $collection = parent::findBy($criteria, $orderBy, $limit, $offset);
        foreach ($collection as $entity) {
            $entity->setValidator($this->validator);
        }

        return $collection;
    }

    public function findOneBy(array $criteria, array $orderBy = null): ?UsesPHPMetaDataTrait
    {
        $entity = parent::findOneBy($criteria, $orderBy);
        if (null === $entity) {
            return null;
        }
        $entity->setValidator($this->validator);

        return $entity;
    }

}
