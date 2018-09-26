<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

/**
 * This process handles updating the namespace for deeply nested Entities,
 * eg ones that are not in the root Entities namespace
 */
class ReplaceEntitiesSubNamespaceProcess implements ProcessInterface
{
    /**
     * @var string|null
     */
    private $entitySubNamespace;
    /**
     * @var string
     */
    private $projectRootNamespace;

    public function setEntityFqn(string $entityFqn)
    {
        if (false === \ts\stringContains($entityFqn, '\\Entities\\')) {
            throw new \RuntimeException(
                'This does not look like an Entity FQN: ' . $entityFqn
            );
        }
        $this->setEntitySubNamespaceFromEntityFqn($entityFqn);

        return $this;
    }

    private function setEntitySubNamespaceFromEntityFqn(string $entityFqn): void
    {
        $fromEntities = substr($entityFqn, \ts\strpos($entityFqn, '\\Entities\\'));
        $exploded     = explode('\\', $fromEntities);
        array_pop($exploded);
        array_shift($exploded);
        array_shift($exploded);
        $exploded = array_filter($exploded);
        if ([] === $exploded) {
            return;
        }
        $this->entitySubNamespace = implode('\\', $exploded);
    }

    public function setProjectRootNamespace(string $projectRootNamespace)
    {
        $this->projectRootNamespace               = $projectRootNamespace;
        $this->projectRootNamespaceForwardSlashes = str_replace('\\', '/', $projectRootNamespace);
    }

    public function run(File\FindReplace $findReplace): void
    {
        if (null === $this->entitySubNamespace) {
            return;
        }
        if (null === $this->projectRootNamespace) {
            throw new \RuntimeException('You must call setProjectRootNamespace first');
        }
        $this->replaceEntities($findReplace);
        $this->replaceEntity($findReplace);
    }

    private function replaceEntities(File\FindReplace $findReplace): void
    {
        $pattern     =
            $findReplace->convertForwardSlashesToBackSlashes(
                '%' . $this->projectRootNamespaceForwardSlashes . '/Entities(/|;)(?!Abstract)%'
            );
        $replacement = $this->projectRootNamespace . '\\Entities\\' . $this->entitySubNamespace . '$1';
        $findReplace->findReplaceRegex($pattern, $replacement);
    }

    private function replaceEntity(File\FindReplace $findReplace)
    {
        $pattern     = $findReplace->convertForwardSlashesToBackSlashes(
            '%' . $this->projectRootNamespaceForwardSlashes . '/Entity/([^/]+?)(/|;)(?!Fixtures)(?!Abstract)%'
        );
        $replacement = $this->projectRootNamespace . '\\Entity\\\$1\\' . $this->entitySubNamespace . '$2';
        $findReplace->findReplaceRegex($pattern, $replacement);
    }
}
