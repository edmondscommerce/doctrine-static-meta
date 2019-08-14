<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

class ReplaceTypeHintsProcess implements ProcessInterface
{
    /**
     * @var CodeHelper
     */
    private $codeHelper;
    /**
     * @var string
     */
    private $mappingHelperType;
    /**
     * @var bool
     */
    private $nullable;
    /**
     * @var string
     */
    private $phpType;

    public function __construct(CodeHelper $codeHelper, string $phpType, string $mappingHelperType, $defaultValue)
    {
        $this->codeHelper        = $codeHelper;
        $this->phpType           = $phpType;
        $this->mappingHelperType = $mappingHelperType;
        $this->nullable          = (null === $defaultValue);
    }

    public function run(File\FindReplace $findReplace): void
    {
        $file     = $findReplace->getFile();
        $contents = $file->getContents();
        $contents = $this->codeHelper->replaceTypeHintsInContents(
            $contents,
            $this->phpType,
            $this->mappingHelperType,
            $this->nullable
        );
        $file->setContents($contents);
    }
}
