<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use Doctrine\Common\Inflector\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\model\GenerateableInterface;
use Nette\PhpGenerator\ClassType;
use RuntimeException;
use function file_put_contents;
use function in_array;
use function preg_match;
use function str_replace;

/**
 * Class CodeHelper
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
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

    public function propertyIsh(string $name): string
    {
        return lcfirst($this->classy($name));
    }

    public function classy(string $name): string
    {
        return MappingHelper::getInflector()->classify($name);
    }

    public function consty(string $name): string
    {
        if (0 === preg_match('%[^A-Z_]%', $name)) {
            return $name;
        }

        return strtoupper(MappingHelper::getInflector()->tableize($name));
    }

    /**
     * @param string $filePath
     *
     * @throws RuntimeException
     */
    public function tidyNamespacesInFile(string $filePath): void
    {
        $contents = \ts\file_get_contents($filePath);
        $contents = preg_replace_callback(
        /**
         * @param $matches
         *
         * @return string
         */
            '%(namespace|use) (.+?);%',
            function ($matches): string {
                return $matches[1] . ' ' . $this->namespaceHelper->tidy($matches[2]) . ';';
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
     * @param string $dbalType
     * @param bool   $isNullable
     */
    public function replaceTypeHintsInFile(
        string $filePath,
        string $type,
        string $dbalType,
        bool $isNullable
    ): void {
        $contents = \ts\file_get_contents($filePath);
        $contents = $this->replaceTypeHintsInContents($contents, $type, $dbalType, $isNullable);
        file_put_contents($filePath, $contents);
    }

    public function replaceTypeHintsInContents(
        string $contents,
        string $type,
        string $dbalType,
        bool $isNullable
    ): string {
        $search = [
            'private string ',
            'protected string ',
            'public string ',
            ': string;',
            '(string $',
            ': string {',
            ": string\n",
            '@var string',
            '@return string',
            '@param string',

        ];

        $replaceNormal   = [
            "private $type ",
            "protected $type ",
            "public $type ",
            ": $type;",
            "($type $",
            ": $type {",
            ": $type\n",
            "@var $type",
            "@return $type",
            "@param $type",
        ];
        $replaceNullable = [
            "private ?$type ",
            "protected ?$type ",
            "public ?$type ",
            ": ?$type;",
            "(?$type $",
            ": ?$type {",
            ": ?$type\n",
            "@var $type|null",
            "@return $type|null",
            "@param $type|null",
        ];
        $replaceRemove   = [
            ';',
            '($',
            ' {',
            "\n",
            '',
            '',
            '',
        ];

        $replace = $replaceNormal;

        if (in_array($dbalType, MappingHelper::MIXED_TYPES, true)) {
            $replace = $replaceRemove;
        } elseif ($isNullable) {
            $replace = $replaceNullable;
        }

        $contents = str_replace(
            $search,
            $replace,
            $contents
        );

        return $contents;
    }

    /**
     * @deprecated this is the Gossi Codegen generator which is now removed, see the ::write method instead which uses
     *             the Nette Codegen ClassType instead
     *
     */
    public function generate(
        GenerateableInterface $generateable,
        string $filePath,
        ?PostProcessorInterface $postProcessor = null
    ): void {
        $generator = new CodeFileGenerator(
            [
                'generateDocblock'   => false,
                'declareStrictTypes' => true,
            ]
        );

        $generated = $generator->generate($generateable);
        $generated = $this->postProcessGeneratedCode($generated, $postProcessor);
        file_put_contents($filePath, $generated);
    }

    public function write(
        ClassType $classType,
        string $filePath,
        ?PostProcessorInterface $postProcessor = null
    ): void {
        $generated = (string)$classType;
        $generated = $this->postProcessGeneratedCode($generated, $postProcessor);
        file_put_contents($filePath, $generated);
    }

    /**
     * Fix niggles with code that is generated by gossi/php-code-generator
     *
     * @param string                      $generated
     *
     * @param PostProcessorInterface|null $postProcessor
     *
     * @return string
     */
    public function postProcessGeneratedCode(string $generated, ?PostProcessorInterface $postProcessor = null): string
    {

        $generated = $this->fixSuppressWarningsTags($generated);
        $generated = $this->breakImplementsAndExtendsOntoLines($generated);
        $generated = $this->makeConstsPublic($generated);
        $generated = $this->constArraysOnMultipleLines($generated);
        $generated = $this->phpcsIgnoreUseSection($generated);
        $generated = $this->declareStrictFirstLine($generated);
        if (null !== $postProcessor) {
            $generated = $postProcessor($generated);
        }

        return $generated;
    }

    public function fixSuppressWarningsTags(string $generated): string
    {
        return str_replace('SuppressWarnings (', 'SuppressWarnings(', $generated);
    }

    public function breakImplementsAndExtendsOntoLines(string $generated): string
    {
        return preg_replace_callback(
            '%(class|interface) (.+?) (implements|extends) (.+?){%s',
            static function ($matches) {
                return $matches[1] . ' ' . $matches[2] . ' ' . $matches[3] . ' '
                       . "\n    "
                       . trim(
                           implode(
                               ",\n    ",
                               explode(
                                   ', ',
                                   $matches[4]
                               )
                           )
                       ) . "\n{";
            },
            $generated
        );
    }

    public function makeConstsPublic(string $generated): string
    {
        return str_replace("\tconst", "\tpublic const", $generated);
    }

    public function constArraysOnMultipleLines(string $generated): string
    {
        return preg_replace_callback(
            "%(.*?)const ([A-Z_0-9]+?) = \[([^\]]+?)\];%",
            static function ($matches) {
                return $matches[1] . 'const ' . $matches[2] . " = [\n        "
                       . trim(
                           implode(
                               ",\n        ",
                               explode(
                                   ', ',
                                   $matches[3]
                               )
                           )
                       ) . "\n    ];";
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
            "namespace \$1;\n// phpcs:disable Generic.Files.LineLength.TooLong\$2// phpcs:enable\n\$3 ",
            $generated
        );
    }

    public function declareStrictFirstLine(string $generated): string
    {
        return preg_replace('%php\s+declare%', 'php declare', $generated);
    }

    public function getGetterMethodNameForBoolean(string $fieldName): string
    {
        if (0 === stripos($fieldName, 'is')) {
            return lcfirst($fieldName);
        }

        if (0 === stripos($fieldName, 'has')) {
            return lcfirst($fieldName);
        }

        return 'is' . ucfirst($fieldName);
    }
}
