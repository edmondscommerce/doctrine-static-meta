<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Pipeline;

class AbstractEntityTestCreator extends AbstractCreator
{
    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . '/tests/Entities/AbstractEntityTest.php';

    /**
     * In this method we register all the process steps that we want to rnu against the file
     *
     * By default this registers the ReplaceNameProcess which is almost certainly required. Other processes can be
     * registered as required
     */
    protected function configurePipeline(): void
    {
        $this->pipeline = new Pipeline($this->findReplaceFactory);
        $this->registerReplaceProjectRootNamespace();
    }
}