<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Pipeline;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceNameProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceProjectRootNamespaceProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractCreator implements CreatorInterface
{
    protected const TEMPLATE_ENTITY_NAME = 'TemplateEntity';

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

    public const SRC_DIR = 'src';

    public const TEST_DIR = 'tests';

    /**
     * @var string|null
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
     * @var Pipeline
     */
    protected $pipeline;
    /**
     * @var string|null
     */
    protected $projectRootNamespace;
    /**
     * @var string|null
     */
    protected $projectRootDirectory;

    public function __construct(
        FileFactory $fileFactory,
        NamespaceHelper $namespaceHelper,
        File\Writer $fileWriter,
        Config $config,
        FindReplaceFactory $findReplaceFactory
    ) {
        $this->fileFactory     = $fileFactory;
        $this->namespaceHelper = $namespaceHelper;
        $this->fileWriter      = $fileWriter;
        $this->setProjectRootNamespace($this->namespaceHelper->getProjectRootNamespaceFromComposerJson());
        $this->setProjectRootDirectory($config::getProjectRootDirectory());
        $this->findReplaceFactory = $findReplaceFactory;
    }

    public function setProjectRootNamespace(string $projectRootNamespace): self
    {
        $this->projectRootNamespace = $projectRootNamespace;
        $this->fileFactory->setProjectRootNamespace($projectRootNamespace);

        return $this;
    }

    public function setProjectRootDirectory(string $projectRootDirectory): self
    {
        $this->projectRootDirectory = $projectRootDirectory;
        $this->fileFactory->setProjectRootDirectory($projectRootDirectory);

        return $this;
    }

    public function createTargetFileObject(string $newObjectFqn = null): self
    {
        if (null === $newObjectFqn && null === $this->newObjectFqn) {
            throw new \RuntimeException(
                'No new objectFqn either set previously or passed in'
            );
        }
        if (null !== $newObjectFqn) {
            $this->newObjectFqn = $newObjectFqn;
        }
        $this->templateFile = $this->fileFactory->createFromExistingPath(static::TEMPLATE_PATH);
        $this->targetFile   = $this->fileFactory->createFromFqn($this->newObjectFqn);
        $this->updateRootDirOnTargetFile();
        $this->setTargetContentsWithTemplateContents();
        $this->configurePipeline();
        $this->pipeline->run($this->targetFile);

        return $this;
    }

    /**
     * Where the template file is in tests, we need to fix that in the target file
     */
    private function updateRootDirOnTargetFile(): void
    {
        $realTemplateTestsPath = realpath(self::ROOT_TEMPLATE_PATH . self::TEST_DIR);
        if (0 === \strpos($this->templateFile->getPath(), $realTemplateTestsPath)) {
            $updatedPath = str_replace(
                '/src/',
                '/tests/',
                $this->targetFile->getPath()
            );
            $this->targetFile->setPath($updatedPath);
        }
    }

    protected function setTargetContentsWithTemplateContents()
    {
        $this->targetFile->setContents(
            $this->templateFile->loadContents()
                               ->getContents()
        );
    }

    /**
     * In this method we register all the process steps that we want to rnu against the file
     *
     * By default this registers the ReplaceNameProcess which is almost certainly required. Other processes can be
     * registered as required
     */
    protected function configurePipeline(): void
    {
        $this->pipeline = new Pipeline($this->findReplaceFactory);
        $this->registerReplaceName();
        $this->registerReplaceProjectRootNamespace();
    }

    protected function registerReplaceName(): void
    {
        $replaceName = new ReplaceNameProcess();
        $replaceName->setArgs(static::FIND_NAME, $this->namespaceHelper->basename($this->newObjectFqn));
        $this->pipeline->register($replaceName);
    }

    protected function registerReplaceProjectRootNamespace()
    {
        $replaceTemplateNamespace = new ReplaceProjectRootNamespaceProcess();
        $replaceTemplateNamespace->setProjectRootNamespace($this->projectRootNamespace);
        $this->pipeline->register($replaceTemplateNamespace);
    }

    public function getTargetFile(): File
    {
        return $this->targetFile;
    }

    /**
     * Write the file only if it doesn't already exist
     *
     * @return string
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function writeIfNotExists(): string
    {
        if ($this->targetFile->exists()) {
            return $this->targetFile->getPath();
        }

        return $this->write();
    }

    /**
     * Write the file and return the generated path
     *
     * @return string
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function write(): string
    {
        $this->targetFile->removeIfExists();

        return $this->fileWriter->write($this->targetFile);
    }

    protected function registerEntityReplaceName(string $entityFqn): void
    {
        $replaceName = new ReplaceNameProcess();
        $replaceName->setArgs(self::TEMPLATE_ENTITY_NAME, $this->namespaceHelper->basename($entityFqn));
        $this->pipeline->register($replaceName);
    }
}
