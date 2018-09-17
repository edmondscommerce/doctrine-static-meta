<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entities;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\CreatorInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesNamespaceProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntityIdFieldProcess;

class EntityCreator extends AbstractCreator
{
    public const FIND_NAME = 'TemplateEntity';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . '/' . CreatorInterface::SRC_FOLDER . '/Entities/'
                                 . self::FIND_NAME . '.php';

    /**
     * @var ReplaceEntityIdFieldProcess|null
     */
    private $replaceIdFieldProcess;

    public function configurePipeline(): void
    {
        parent::configurePipeline();
        $this->registerReplaceEntitiesNamespaceProcess();
        if (null !== $this->replaceIdFieldProcess) {
            $this->pipeline->register($this->replaceIdFieldProcess);
        }
    }


    /**
     * If you want to replace the ID field, you must set a preconfigured replace process object using this method
     *
     * @param mixed $replaceIdFieldProcess
     *
     * @return EntityCreator
     */
    public function setReplaceIdFieldProcess(ReplaceEntityIdFieldProcess $replaceIdFieldProcess)
    {
        $this->replaceIdFieldProcess = $replaceIdFieldProcess;

        return $this;
    }


}