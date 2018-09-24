<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

class BootstrapCreator extends AbstractCreator
{
    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . '/tests/bootstrap.php';

    public function createTargetFileObject(?string $newObjectFqn = null): AbstractCreator
    {
        if (null !== $newObjectFqn) {
            throw new \RuntimeException('You should not pass a new object FQN to this creator');
        }
        $this->templateFile = $this->fileFactory->createFromExistingPath(static::TEMPLATE_PATH);
        $this->targetFile   = new File(
            \str_replace(
                self::ROOT_TEMPLATE_PATH,
                $this->projectRootDirectory,
                static::TEMPLATE_PATH)
        );
        $this->setTargetContentsWithTemplateContents();

        return $this;
    }
}