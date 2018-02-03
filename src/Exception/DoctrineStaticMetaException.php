<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Exception;

use EdmondsCommerce\DoctrineStaticMeta\Config;

class DoctrineStaticMetaException extends \Exception
{
    public function getTraceAsStringRelativePath(): string
    {
        return "\n\n".str_replace(
                Config::getProjectRootDirectory(),
                '',
                parent::getTraceAsString()
            )."\n\n";
    }
}
