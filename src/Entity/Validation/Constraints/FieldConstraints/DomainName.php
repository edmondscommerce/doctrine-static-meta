<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\FieldConstraints;

use Symfony\Component\Validator\Constraint;

class DomainName extends Constraint
{
    public const MESSAGE = 'The domain name { value } is not valid.';

    public const INVALID_DOMAIN_ERROR = '5819dfc4-b66c-11e8-96f8-529269fb1459';

    public $message = self::MESSAGE;

    /**
     * @param string[] $groups
     */
    public function __construct($options = null, array $groups = null, $payload = self::MESSAGE)
    {
        parent::__construct($options, $groups, $payload);
    }


}
