<?php declare(strict_types=1);

namespace TemplateNamespace\EntityRepositories;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaDataTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractEntityRepository extends EntityRepository
{
}
