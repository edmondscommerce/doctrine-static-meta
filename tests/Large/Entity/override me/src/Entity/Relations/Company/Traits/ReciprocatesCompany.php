<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Company\Traits;

// phpcs:disable
use My\Test\Project\Entities\Company as Company;
use My\Test\Project\Entity\Interfaces\CompanyInterface;
use My\Test\Project\Entity\Relations\Company\Interfaces\ReciprocatesCompanyInterface;

/**
 * Trait ReciprocatesCompany
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package Test\Code\Generator\Entity\Relations\Company\Traits
 */
// phpcs:enable
trait ReciprocatesCompany
{
    /**
     * This method needs to set the relationship on the company to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Company|null $company
     *
     * @return ReciprocatesCompanyInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnCompany(
        CompanyInterface $company
    ): ReciprocatesCompanyInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $company::getDoctrineStaticMeta()->getSetters();
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
                . \get_class($this) . ' to Company'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $company->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the company to this entity.
     *
     * @param Company $company
     *
     * @return ReciprocatesCompanyInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnCompany(
        CompanyInterface $company
    ): ReciprocatesCompanyInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $company->$method($this, false);

        return $this;
    }
}
