<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Repositories;
// phpcs:disable -- line length
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaDataTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractEntityRepository extends EntityRepository
{
    // phpcs:enable
}
