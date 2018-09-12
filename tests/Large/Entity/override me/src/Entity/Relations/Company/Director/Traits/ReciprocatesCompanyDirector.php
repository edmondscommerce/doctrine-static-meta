<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Company\Director\Traits;

// phpcs:disable
use My\Test\Project\Entities\Company\Director as CompanyDirector;
use My\Test\Project\Entity\Interfaces\Company\DirectorInterface;
use My\Test\Project\Entity\Relations\Company\Director\Interfaces\ReciprocatesCompanyDirectorInterface;

/**
 * Trait ReciprocatesCompanyDirector
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package Test\Code\Generator\Entity\Relations\CompanyDirector\Traits
 */
// phpcs:enable
trait ReciprocatesCompanyDirector
{
    /**
     * This method needs to set the relationship on the companyDirector to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param CompanyDirector|null $companyDirector
     *
     * @return ReciprocatesCompanyDirectorInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnCompanyDirector(
        DirectorInterface $companyDirector
    ): ReciprocatesCompanyDirectorInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $companyDirector::getDoctrineStaticMeta()->getSetters();
        $setter   = null;
        foreach ($setters as $method) {
            if (0 === \strcasecmp($method, 'add' . $singular)) {
                $setter = $method;
                break;
            }
            if (0 === \strcasecmp($method, 'set' . $singular)) {
                $setter = $method;
                break;
            }
        }
        if (null === $setter) {
            throw new \RuntimeException(
                'Failed to find the correct method '
                . 'when attempting to reciprocate the relation from '
                . \get_class($this) . ' to CompanyDirector'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $companyDirector->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the companyDirector to this entity.
     *
     * @param CompanyDirector $companyDirector
     *
     * @return ReciprocatesCompanyDirectorInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnCompanyDirector(
        DirectorInterface $companyDirector
    ): ReciprocatesCompanyDirectorInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $companyDirector->$method($this, false);

        return $this;
    }
}
