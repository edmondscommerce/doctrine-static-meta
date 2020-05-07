<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Generator;
use RuntimeException;
use Symfony\Component\Finder\Finder;

class EntityFormatter
{
    /**
     * @var FileFactory
     */
    private FileFactory $fileFactory;
    /**
     * @var string
     */
    private string $pathToProjectRoot;

    public function __construct(FileFactory $fileFactory)
    {
        $this->fileFactory = $fileFactory;
    }

    public function run(): void
    {
        foreach ($this->entityFileGenerator() as $entityFile) {
            $this->organiseTraits($entityFile);
            $entityFile->putContents();
        }
    }

    /**
     * @return Generator|File[]
     * @throws DoctrineStaticMetaException
     */
    private function entityFileGenerator(): Generator
    {
        $finder = new Finder();
        $finder->in($this->pathToProjectRoot . '/src/Entities');
        $finder->ignoreDotFiles(true);
        $finder->files();
        foreach ($finder as $file) {
            $entityFile = $this->fileFactory->createFromExistingPath($file->getPathname());
            $entityFile->loadContents();
            yield $entityFile;
        }
    }

    private function organiseTraits(File $entityFile): void
    {
        $body      = $this->getEntityBody($entityFile);
        $extraCode = $body;
        preg_match_all('%\s+?use [^;]+;%', $body, $traitMatches);
        $traits = [];
        foreach ($traitMatches[0] as $traitLine) {
            $extraCode                                 = str_replace($traitLine, '', $extraCode);
            $traits[$this->getTraitType($traitLine)][] = trim($traitLine);
        }
        ksort($traits);
        $traitsBody = '';
        foreach ($traits as $title => $lines) {
            $comment    = "\n" . substr($title, 2);
            $extraCode  = str_replace($comment, '', $extraCode);
            $traitsBody .= $comment;
            $traitsBody .= "\n    " . implode("\n    ", $lines) . "\n";
        }
        $extraCode     = \ts\preg_replace("%\n{3,}%", "\n\n", $extraCode);
        $organisedBody = "$traitsBody\n$extraCode";
        $organisedBody = str_replace($body, $organisedBody, $entityFile->getContents());
        $organisedBody = \ts\preg_replace("%\n+?}%", "\n}", $organisedBody);
        $entityFile->setContents($organisedBody);
    }

    private function getEntityBody(File $entityFile): string
    {
        preg_match('%class[^{].+?{(.+)}%s', $entityFile->getContents(), $matches);

        return $matches[1];
    }

    private function getTraitType(string $traitLine): string
    {
        $commentStart = "    /**\n     *";
        $commentEnd   = "\n     */";
        if (false !== \ts\stringContains($traitLine, 'Embeddable')) {
            return "5 $commentStart Embeddables $commentEnd";
        }
        if (false !== \ts\stringContains($traitLine, 'use HasRequired')) {
            return "1 $commentStart Required Relations $commentEnd";
        }
        if (false !== \ts\stringContains($traitLine, 'use Has')) {
            return "2 $commentStart Relations $commentEnd";
        }
        if (false !== \ts\stringContains($traitLine, 'use DSM\\Traits')) {
            return "0 $commentStart DSM Traits $commentEnd";
        }
        if (false !== \ts\stringContains($traitLine, 'use DSM\\Fields')) {
            return "3 $commentStart DSM Fields $commentEnd";
        }
        if (false !== \ts\stringContains($traitLine, 'FieldTrait')) {
            return "4 $commentStart Fields $commentEnd";
        }

        throw new RuntimeException('Failed finding trait type for line ' . $traitLine);
    }

    /**
     * @param string $pathToProjectRoot
     *
     * @return EntityFormatter
     */
    public function setPathToProjectRoot(string $pathToProjectRoot): EntityFormatter
    {
        $this->pathToProjectRoot = $pathToProjectRoot;

        return $this;
    }
}
