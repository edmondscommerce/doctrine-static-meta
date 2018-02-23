<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\model\GenerateableInterface;

class CodeHelper
{

    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;

    public function __construct(NamespaceHelper $namespaceHelper)
    {
        $this->namespaceHelper = $namespaceHelper;
    }

    /**
     * Fix niggles with code that is generated by gossi/php-code-generator
     *
     * @param string $generated
     *
     * @return string
     */
    public function postProcessGeneratedCode(string $generated): string
    {

        $generated = $this->fixSuppressWarningsTags($generated);
        $generated = $this->breakImplementsOntoLines($generated);
        $generated = $this->makeConstsPublic($generated);
        $generated = $this->constArraysOnMultipleLines($generated);
        $generated = $this->phpcsIgnoreUseSection($generated);

        return $generated;
    }

    public function fixSuppressWarningsTags(string $generated): string
    {
        return str_replace('SuppressWarnings (', 'SuppressWarnings(', $generated);
    }

    public function makeConstsPublic(string $generated): string
    {
        return preg_replace('%^([ ]+?)const%', '$1public const', $generated);
    }

    public function breakImplementsOntoLines(string $generated): string
    {
        return preg_replace_callback(
            '%class (.+?) implements (.+?){%s',
            function ($matches)
            {
                return 'class '.$matches[1].' implements '
                    ."\n    "
                    .trim(
                        implode(
                            ",\n    ",
                            explode(
                                ', ',
                                $matches[2]
                            )
                        )
                    )."\n{";
            },
            $generated
        );
    }

    public function constArraysOnMultipleLines(string $generated): string
    {
        return preg_replace_callback(
            "%(.*?)const ([A-Z_0-9]+?) = \[([^\]]+?)\];%",
            function ($matches)
            {
                return $matches[1].'const '.$matches[2]." = [\n        "
                    .trim(
                        implode(
                            ",\n        ",
                            explode(
                                ', ',
                                $matches[3]
                            )
                        )
                    )."\n    ];";
            },
            $generated
        );
    }

    /**
     * Use section can become long and fail line length limits. I don't care about line length here
     *
     * @param string $generated
     *
     * @return string
     */
    public function phpcsIgnoreUseSection(string $generated): string
    {
        return preg_replace(
            '%namespace (.+?);(.+?)(class|trait|interface) %si',
            "namespace \$1;\n// phpcs:disable\$2// phpcs:enable\n\$3 ",
            $generated
        );
    }

    /**
     * Take a potentially non existent path and resolve the relativeness into a normal path
     *
     * @param string $relativePath
     *
     * @return string
     * @throws \RuntimeException
     */
    public function resolvePath(string $relativePath): string
    {
        $path = [];
        $absolute = ($relativePath[0] === '/');
        foreach (explode('/', $relativePath) as $part) {
            // ignore parts that have no value
            if (empty($part) || $part === '.') {
                continue;
            }

            if ($part !== '..') {
                $path[] = $part;
                continue;
            }
            if (count($path) > 0) {
                // going back up? sure
                array_pop($path);
                continue;
            }
            throw new \RuntimeException('Relative path resolves above root path.');
        }

        $return = implode('/', $path);
        if ($absolute) {
            $return = "/$return";
        }

        return $return;
    }

    /**
     * @param string $filePath
     * @throws \RuntimeException
     */
    public function tidyNamespacesInFile(string $filePath): void
    {
        $contents = file_get_contents($filePath);
        $contents = preg_replace_callback(
        /**
         * @param $matches
         * @return string
         */
            '%(namespace|use) (.+?);%',
            function ($matches): string
            {
                return $matches[1].' '.$this->namespaceHelper->tidy($matches[2]).';';
            },
            $contents
        );
        file_put_contents($filePath, $contents);
    }


    /**
     * We use the string type hint as our default in templates
     *
     * This method will then replace those with the updated type
     *
     * @param string $filePath
     * @param string $type
     */
    public function replaceTypeHintsInFile(string $filePath, string $type): void
    {
        $contents = \file_get_contents($filePath);
        $contents = \str_replace(
            [
                ': string;',
                '(string $',
                ': string
    {',
                '@var string',
                '@return string',
                '@param string',

            ],
            [
                ": $type;",
                "($type $",
                ": $type
    {",
                "@var $type",
                "@return $type",
                "@param $type",
            ],
            $contents
        );
        \file_put_contents($filePath, $contents);
    }

    public function generate(GenerateableInterface $generateable, $filePath)
    {
        $generator = new CodeFileGenerator(
            [
                'generateDocblock' => false,
                'declareStrictTypes' => true,
            ]
        );

        $generated = $generator->generate($generateable);
        $generated = $this->postProcessGeneratedCode($generated);
        \file_put_contents($filePath, $generated);
    }
}
