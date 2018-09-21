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

    public function run(File\FindReplace $findReplace): void
    {
        if (null === $this->entitySubNamespace) {
            return;
        }
        $this->replaceEntities($findReplace);
        $this->replaceEntity($findReplace);
    }

    private function replaceEntities(File\FindReplace $findReplace): void
    {
        $pattern     = $findReplace->convertForwardSlashesToBackSlashes('%/Entities(/|;)(?!Abstract)%');
        $replacement = '\\Entities\\' . $this->entitySubNamespace . '$1';
        $findReplace->findReplaceRegex($pattern, $replacement);
    }

    private function replaceEntity(File\FindReplace $findReplace)
    {
        $pattern     = $findReplace->convertForwardSlashesToBackSlashes(
            '%(.+?)/Entity/([^/]+?)(/|;)(?!Fixtures)(?!Abstract)%'
        );
        $replacement = '$1\\Entity\\\$2\\' . $this->entitySubNamespace . '$3';
        $findReplace->findReplaceRegex($pattern, $replacement);
    }
}