<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Director\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Interfaces\Company\DirectorInterface;
use My\Test\Project\Entity\Relations\Company\Director\Interfaces\HasCompanyDirectorsInterface;
use My\Test\Project\Entity\Relations\Company\Director\Interfaces\ReciprocatesCompanyDirectorInterface;

/**
 * Trait HasCompanyDirectorsAbstract
 *
 * The base trait for relations to multiple CompanyDirectors
 *
 * @package Test\Code\Generator\Entity\Relations\CompanyDirector\Traits
 */
// phpcs:enable
trait HasCompanyDirectorsAbstract
{
    /**
     * @var ArrayCollection|DirectorInterface[]
     */
    private $companyDirectors;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForCompanyDirectors(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasCompanyDirectorsInterface::PROPERTY_NAME_COMPANY_DIRECTORS,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForCompanyDirectors(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|DirectorInterface[]
     */
    public function getCompanyDirectors(): Collection
    {
        return $this->companyDirectors;
    }

    /**
     * @param Collection|DirectorInterface[] $companyDirectors
     *
     * @return self
     */
    public function setCompanyDirectors(
        Collection $companyDirectors
    ): HasCompanyDirectorsInterface {
        $this->setEntityCollectionAndNotify(
            'companyDirectors',
            $companyDirectors
        );

        return $this;
    }

    /**
     * @param DirectorInterface|null $companyDirector
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCompanyDirector(
        ?DirectorInterface $companyDirector,
        bool $recip = true
    ): HasCompanyDirectorsInterface {
        if ($companyDirector === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('companyDirectors', $companyDirector);
        if ($this instanceof ReciprocatesCompanyDirectorInterface && true === $recip) {
            $this->reciprocateRelationOnCompanyDirector(
                $companyDirector
            );
        }

        return $this;
    }

    /**
     * @param DirectorInterface $companyDirector
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCompanyDirector(
        DirectorInterface $companyDirector,
        bool $recip = true
    ): HasCompanyDirectorsInterface {
        $this->removeFromEntityCollectionAndNotify('companyDirectors', $companyDirector);
        if ($this instanceof ReciprocatesCompanyDirectorInterface && true === $recip) {
            $this->removeRelationOnCompanyDirector(
                $companyDirector
            );
        }

        return $this;
    }

    /**
     * Initialise the companyDirectors property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initCompanyDirectors()
    {
        $this->companyDirectors = new ArrayCollection();

        return $this;
    }
}
