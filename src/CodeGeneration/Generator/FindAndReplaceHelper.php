<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use Doctrine\Common\Inflector\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

/**
 * Provides functionality to find and replace text in generated code
 *
 * Class FindAndReplaceHelper
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FindAndReplaceHelper
{

    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;

    public function __construct(NamespaceHelper $namespaceHelper)
    {
        $this->namespaceHelper = $namespaceHelper;
    }

    /**
     * @param string $find
     * @param string $replace
     * @param string $filePath
     *
     * @return self
     */
    public function findReplaceRegex(
        string $find,
        string $replace,
        string $filePath
    ): self {
        $contents = \ts\file_get_contents($filePath);
        $contents = preg_replace($find, $replace, $contents);
        file_put_contents($filePath, $contents);

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     * @param string $findName
     *
     * @return self
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function replaceName(
        string $replacement,
        string $filePath,
        $findName = AbstractGenerator::FIND_ENTITY_NAME
    ): self {
        $this->findReplace($findName, $replacement, $filePath);
        $this->findReplace(\lcfirst($findName), \lcfirst($replacement), $filePath);
        $this->findReplace(\strtoupper($findName), \strtoupper($replacement), $filePath);
        $this->findReplace(
            \strtoupper(Inflector::tableize($findName)),
            \strtoupper(Inflector::tableize($replacement)),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $find
     * @param string $replace
     * @param string $filePath
     *
     * @return self
     */
    public function findReplace(
        string $find,
        string $replace,
        string $filePath
    ): self {
        $contents = \ts\file_get_contents($filePath);
        $contents = str_replace($find, $replace, $contents);
        file_put_contents($filePath, $contents);

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return self
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function replacePluralName(string $replacement, string $filePath): self
    {
        $this->findReplace(AbstractGenerator::FIND_ENTITY_NAME_PLURAL, $replacement, $filePath);
        $this->findReplace(\lcfirst(AbstractGenerator::FIND_ENTITY_NAME_PLURAL), \lcfirst($replacement), $filePath);
        $this->findReplace(
            \strtoupper(AbstractGenerator::FIND_ENTITY_NAME_PLURAL),
            \strtoupper($replacement),
            $filePath
        );
        $this->findReplace(
            \strtoupper(Inflector::tableize(AbstractGenerator::FIND_ENTITY_NAME_PLURAL)),
            \strtoupper(Inflector::tableize($replacement)),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return self
     * @throws \RuntimeException
     */
    public function replaceEntitiesNamespace(string $replacement, string $filePath): self
    {
        if (false === \ts\stringContains($replacement, '\\Entities')) {
            throw new \RuntimeException('$replacement ' . $replacement . ' does not contain \\Entities\\');
        }
        $this->findReplace(
            AbstractGenerator::FIND_ENTITIES_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return self
     * @throws \RuntimeException
     */
    public function replaceEntityNamespace(string $replacement, string $filePath): self
    {
        if (false === \ts\stringContains($replacement, '\\Entity')) {
            throw new \RuntimeException('$replacement ' . $replacement . ' does not contain \\Entity\\');
        }
        $this->findReplace(
            AbstractGenerator::FIND_ENTITY_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return self
     * @throws \RuntimeException
     */
    public function replaceFieldTraitNamespace(string $replacement, string $filePath): self
    {
        if (false === \ts\stringContains($replacement, AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE)) {
            throw new \RuntimeException(
                '$replacement ' . $replacement . ' does not contain '
                . AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE
            );
        }
        $this->findReplace(
            AbstractGenerator::FIND_FIELD_TRAIT_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return self
     * @throws \RuntimeException
     */
    public function replaceFieldInterfaceNamespace(string $replacement, string $filePath): self
    {
        if (false === \ts\stringContains($replacement, AbstractGenerator::ENTITY_FIELD_INTERFACE_NAMESPACE)) {
            throw new \RuntimeException(
                '$replacement ' .
                $replacement .
                ' does not contain ' .
                AbstractGenerator::ENTITY_FIELD_INTERFACE_NAMESPACE
            );
        }
        $this->findReplace(
            AbstractGenerator::FIND_FIELD_INTERFACE_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return self
     */
    public function replaceProjectNamespace(string $replacement, string $filePath): self
    {
        $this->findReplace(
            AbstractGenerator::FIND_PROJECT_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

        return $this;
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

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return self
     */
    public function replaceEntityRepositoriesNamespace(string $replacement, string $filePath): self
    {
        $this->findReplace(
            AbstractGenerator::FIND_ENTITY_REPOSITORIES_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return self
     */
    public function replaceEntityInterfaceNamespace(string $replacement, string $filePath): self
    {
        $this->findReplace(
            AbstractGenerator::FIND_ENTITY_INTERFACE_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

        return $this;
    }

    /**
     * Totally replace the defined namespace in a class/trait
     * with a namespace calculated from the path of the file
     *
     * @param string $filePath
     *
     * @param string $srcSubFolderName
     *
     * @param string $projectRootNamespace
     *
     * @return self
     * @throws DoctrineStaticMetaException
     */
    public function setNamespaceFromPath(string $filePath, string $srcSubFolderName, string $projectRootNamespace): self
    {
        $pathForNamespace = substr(
            $filePath,
            \ts\strpos(
                $filePath,
                $srcSubFolderName
            )
            + \strlen($srcSubFolderName)
            + 1
        );
        $pathForNamespace = substr($pathForNamespace, 0, strrpos($pathForNamespace, '/'));
        $namespaceToSet   = $projectRootNamespace
                            . '\\' . implode(
                                '\\',
                                explode(
                                    '/',
                                    $pathForNamespace
                                )
                            );
        $contents         = \ts\file_get_contents($filePath);
        $contents         = preg_replace(
            '%namespace[^:]+?;%',
            "namespace $namespaceToSet;",
            $contents,
            1,
            $count
        );
        if ($count !== 1) {
            throw new DoctrineStaticMetaException(
                'Namespace replace count is ' . $count . ', should be 1 when updating file: ' . $filePath
            );
        }
        file_put_contents($filePath, $contents);

        return $this;
    }
}
