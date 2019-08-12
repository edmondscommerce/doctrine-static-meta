<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Exception;

use EdmondsCommerce\DoctrineStaticMeta\Exception\Traits\RelativePathTraceTrait;
use Exception;

class DoctrineStaticMetaException extends Exception
{
    use RelativePathTraceTrait;
}
