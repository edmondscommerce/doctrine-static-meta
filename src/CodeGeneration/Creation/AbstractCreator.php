<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;

abstract class AbstractCreator implements CreatorInterface
{
    /**
     * The absolute path to the template file residing in the root codeTemplates directory
     */
    protected const TEMPLATE_PATH = 'override me';

    /**
     * The basename of the Template object FQN
     */
    protected const FIND_NAME = 'override me';

    /**
     * The path to the root code templates folder
     */
    public const ROOT_TEMPLATE_PATH = __DIR__ . '/../../../codeTemplates/';

    /**
     * @var string
     */
    protected $newObjectFqn;

    /**
     * @var File
     */
    protected $templateFile;

    /**
     * @var File
     */
    protected $targetFile;
    /**
     * @var FileFactory
     */
    protected $fileFactory;
    /**
     * @var FindReplaceFactory
     */
    protected $findReplaceFactory;
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;
    /**
     * @var File\Writer
     */
    protected $fileWriter;

    /**
     * @var File\FindReplace
     */
    private $findReplace;

    public function __construct(
        FileFactory $fileFactory,
        FindReplaceFactory $findReplaceFactory,
        NamespaceHelper $namespaceHelper,
        File\Writer $fileWriter
    ) {
        $this->fileFactory        = $fileFactory;
        $this->findReplaceFactory = $findReplaceFactory;
        $this->namespaceHelper    = $namespaceHelper;
        $this->fileWriter         = $fileWriter;
    }


    public function setProjectRootNamespace(string $projectRootNamespace): self
    {
        $this->fileFactory->setProjectRootNamespace($projectRootNamespace);

        return $this;
    }

    public function setProjectRootDirectory(string $projectRootDirectory): self
    {
        $this->fileFactory->setProjectRootDirectory($projectRootDirectory);

        return $this;
    }

    public function createTargetFileObject(string $newObjectFqn): self
    {
        $this->newObjectFqn = $newObjectFqn;
        $this->templateFile = $this->fileFactory->createFromExistingPath(static::TEMPLATE_PATH);
        $this->targetFile   = $this->fileFactory->createFromFqn($newObjectFqn);
        $this->findReplace  = $this->findReplaceFactory->create($this->targetFile);
        $this->setTargetContentsWithTemplateContents();
        $this->replaceNameInTarget($newObjectFqn);

        return $this;
    }

    private function setTargetContentsWithTemplateContents()
    {
        $this->targetFile->setContents(
            $this->templateFile->loadContents()
                               ->getContents()
        );
    }

    private function replaceNameInTarget(string $newObjectFqn)
    {
        $this->findReplace->findReplaceName(
            static::FIND_NAME,
            $this->namespaceHelper->basename($newObjectFqn)
        );
    }

    public function getTargetFile(): File
    {
        return $this->targetFile;
    }

    /**
     * Write the file and return the generated path
     *
     * @return string
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function write(): string
    {
        return $this->fileWriter->write($this->targetFile);
    }
}