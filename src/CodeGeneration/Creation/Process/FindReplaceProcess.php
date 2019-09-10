<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;

/**
 * Very simple find/replace process
 */
class FindReplaceProcess implements ProcessInterface
{
    /**
     * @var string
     */
    private $find;
    /**
     * @var string
     */
    private $replace;

    /**
     * @param string $find
     * @param string $replace
     */
    public function __construct(string $find, string $replace)
    {
        $this->find    = $find;
        $this->replace = $replace;
    }

    public function run(File\FindReplace $findReplace): void
    {
        $findReplace->findReplace($this->find, $this->replace);
    }
}
