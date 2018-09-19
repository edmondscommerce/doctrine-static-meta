<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

use Doctrine\Common\Inflector\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

/**
 * New way of handling find and replace
 * Replacement for
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
        $this->findReplace($singularFindName, $singularReplaceName);
        $this->findReplace(\lcfirst($singularFindName), \lcfirst($singularReplaceName));
        $this->findReplace(\strtoupper($singularFindName), \strtoupper($singularReplaceName));
        $this->findReplace(
            \strtoupper(Inflector::tableize($singularFindName)),
            \strtoupper(Inflector::tableize($singularReplaceName))
        );

        $pluralFindName    = $this->getPlural($singularFindName);
        $pluralReplaceName = $this->getPlural($singularReplaceName);
        $this->findReplace($pluralFindName, $pluralReplaceName);
        $this->findReplace(\lcfirst($pluralFindName), \lcfirst($pluralReplaceName));
        $this->findReplace(\strtoupper($pluralFindName), \strtoupper($pluralReplaceName));
        $this->findReplace(
            \strtoupper(Inflector::tableize($pluralFindName)),
            \strtoupper(Inflector::tableize($pluralReplaceName))
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
    public function findReplace(string $find, string $replace)
    {
        $contents = $this->file->getContents();
        $contents = \str_replace($find, $replace, $contents);
        $this->file->setContents($contents);

        return $this;
    }

    private function getPlural(string $singular): string
    {
        $plural = Inflector::pluralize($singular);
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
    public function findReplaceRegex(string $find, string $replace)
    {
        $contents = $this->file->getContents();
        $contents = \preg_replace($find, $replace, $contents);
        $this->file->setContents($contents);
    }

    /**
     * Simply enough, pass in a string that contains slashes and this will escape it for use in Regex
     *
     * @param string $input
     *
     * @return mixed
     */
    public function escapeSlashesForRegex(string $input)
    {
        return \str_replace('\\', '\\\\', $input);
    }
}