<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

class Pipeline
{
    /**
     * @var FindReplaceFactory
     */
    protected $findReplaceFactory;
    /**
     * @var array|ProcessInterface[]
     */
    private $processes = [];

    public function __construct(FindReplaceFactory $findReplaceFactory)
    {
        $this->findReplaceFactory = $findReplaceFactory;
    }

    public function register(ProcessInterface $process): self
    {
        $this->processes[] = $process;

        return $this;
    }

    public function run(File $file)
    {
        $findReplace = $this->findReplaceFactory->create($file);
        foreach ($this->processes as $process) {
            $process->run($findReplace);
        }
    }
}