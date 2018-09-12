<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Director\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Company\Director as CompanyDirector;
use My\Test\Project\Entity\Interfaces\Company\DirectorInterface;
use My\Test\Project\Entity\Relations\Company\Director\Interfaces\HasCompanyDirectorInterface;
use My\Test\Project\Entity\Relations\Company\Director\Interfaces\ReciprocatesCompanyDirectorInterface;

/**
 * Trait HasCompanyDirectorAbstract
 *
 * The base trait for relations to a single CompanyDirector
 *
 * @package Test\Code\Generator\Entity\Relations\CompanyDirector\Traits
 */
// phpcs:enable
trait HasCompanyDirectorAbstract
{
    /**
     * @var CompanyDirector|null
     */
    private $companyDirector;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForCompanyDirector(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForCompanyDirector(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasCompanyDirectorInterface::PROPERTY_NAME_COMPANY_DIRECTOR,
            new Valid()
        );
    }

    /**
     * @param null|DirectorInterface $companyDirector
     * @param bool                         $recip
     *
     * @return HasCompanyDirectorInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCompanyDirector(
        ?DirectorInterface $companyDirector = null,
        bool $recip = true
    ): HasCompanyDirectorInterface {
        if (
            $this instanceof ReciprocatesCompanyDirectorInterface
            && true === $recip
        ) {
            if (!$companyDirector instanceof EntityInterface) {
                $companyDirector = $this->getCompanyDirector();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            $companyDirector->$remover($this, false);
        }

        return $this->setCompanyDirector(null, false);
    }

    /**
     * @return DirectorInterface|null
     */
    public function getCompanyDirector(): ?DirectorInterface
    {
        return $this->companyDirector;
    }

    /**
     * @param DirectorInterface|null $companyDirector
     * @param bool                         $recip
     *
     * @return HasCompanyDirectorInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCompanyDirector(
        ?DirectorInterface $companyDirector,
        bool $recip = true
    ): HasCompanyDirectorInterface {

        $this->setEntityAndNotify('companyDirector', $companyDirector);
        if (
            $this instanceof ReciprocatesCompanyDirectorInterface
            && true === $recip
            && null !== $companyDirector
        ) {
            $this->reciprocateRelationOnCompanyDirector($companyDirector);
        }

        return $this;
    }
}
