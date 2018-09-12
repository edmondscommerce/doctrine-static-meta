<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DomainNameValidator extends ConstraintValidator
{

    /**
     * Checks if the passed value is valid.
     *
     * @param string     $domainName
     * @param Constraint $constraint The constraint for the validation
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validate($domainName, Constraint $constraint): void
    {
        if (false === filter_var($domainName, FILTER_VALIDATE_DOMAIN)
            || false === \ts\stringContains($domainName, '.')
            || false !== \ts\stringContains($domainName, '//')
        ) {
            $this->context->buildViolation(sprintf(DomainName::MESSAGE, $this->formatValue($domainName)))
                          ->setParameter('{{ value }}', $this->formatValue($domainName))
                          ->setCode(DomainName::INVALID_DOMAIN_ERROR)
                          ->addViolation();
        }
    }
}