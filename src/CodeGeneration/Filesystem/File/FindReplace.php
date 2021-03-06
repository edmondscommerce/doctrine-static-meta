<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

use Doctrine\Common\Inflector\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

use function lcfirst;
use function preg_replace;
use function str_replace;
use function strtoupper;

/**
 * New way of handling find and replace
 * Replacement for FindAndReplaceHelper
 *
 * @see \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class FindReplace
{
    /**
     * @var File
     */
    protected $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * Find all instances of a name in the various code styles
     *
     * Handles replacing both singular and plural replacements
     *
     * @param string $singularFindName
     * @param string $singularReplaceName
     *
     * @return FindReplace
     */
    public function findReplaceName(
        string $singularFindName,
        string $singularReplaceName
    ): self {
        $singularFindName    = MappingHelper::getInflector()->classify($singularFindName);
        $singularReplaceName = MappingHelper::getInflector()->classify($singularReplaceName);
        $this->findReplace($singularFindName, $singularReplaceName);
        $this->findReplace(lcfirst($singularFindName), lcfirst($singularReplaceName));
        $this->findReplace(strtoupper($singularFindName), strtoupper($singularReplaceName));
        $this->findReplace(
            strtoupper(MappingHelper::getInflector()->tableize($singularFindName)),
            strtoupper(MappingHelper::getInflector()->tableize($singularReplaceName))
        );

        $pluralFindName    = $this->getPlural($singularFindName);
        $pluralReplaceName = $this->getPlural($singularReplaceName);
        $this->findReplace($pluralFindName, $pluralReplaceName);
        $this->findReplace(lcfirst($pluralFindName), lcfirst($pluralReplaceName));
        $this->findReplace(strtoupper($pluralFindName), strtoupper($pluralReplaceName));
        $this->findReplace(
            strtoupper(MappingHelper::getInflector()->tableize($pluralFindName)),
            strtoupper(MappingHelper::getInflector()->tableize($pluralReplaceName))
        );

        return $this;
    }

    /**
     * Find and replace using simple case sensitive str_replace
     *
     * @param string $find
     * @param string $replace
     *
     * @return FindReplace
     */
    public function findReplace(string $find, string $replace): FindReplace
    {
        $contents = $this->file->getContents();
        $contents = str_replace($find, $replace, $contents);
        $this->file->setContents($contents);

        return $this;
    }

    private function getPlural(string $singular): string
    {
        $plural = MappingHelper::getInflector()->pluralize($singular);
        if ($plural === $singular) {
            $plural = $singular . 's';
        }

        return $plural;
    }

    /**
     * Find and replace using preg_replace
     *
     * @param string $find
     * @param string $replace
     */
    public function findReplaceRegex(string $find, string $replace): void
    {
        $contents = $this->file->getContents();
        $contents = preg_replace($find, $replace, $contents, -1/*, $count*/);

        $this->file->setContents($contents);
    }

    /**
     * Simply enough, pass in a string that contains slashes and this will escape it for use in Regex
     *
     * @param string $regexPattern
     *
     * @return mixed
     */
    public function escapeSlashesForRegex(string $regexPattern)
    {
        return str_replace('\\', '\\\\', $regexPattern);
    }

    /**
     * Trying to make namespace based regexes slightly easier to work with
     *
     * @param string $regexPattern
     *
     * @return mixed
     */
    public function convertForwardSlashesToBackSlashes(string $regexPattern)
    {
        return str_replace('/', '\\\\', $regexPattern);
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }
}
