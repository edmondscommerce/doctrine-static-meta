<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Exception\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

trait RelativePathTraceTrait
{
    public function getTraceAsStringRelativePath(): string
    {
        try {
            return "\n\n".str_replace(
                Config::getProjectRootDirectory(),
                '',
                parent::getTraceAsString()
            )."\n\n";
        } catch (DoctrineStaticMetaException $e) {
            return parent::getTraceAsString();
        }
    }
}
