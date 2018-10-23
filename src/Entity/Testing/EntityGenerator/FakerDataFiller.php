<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator;

use Doctrine\DBAL\Types\Type;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Faker;
use Faker\ORM\Doctrine\ColumnTypeGuesser;

class FakerDataFiller
{
    /**
     * @var Faker\Generator
     */
    private static $generator;

    /**
     * These two are used to keep track of unique fields and ensure we dont accidently make apply none unique values
     *
     * @var array
     */
    private static $uniqueStrings = [];
    /**
     * @var int
     */
    private static $uniqueInt;
    /**
     * An array of fieldNames to class names that are to be instantiated as column formatters as required
     *
     * @var array|string[]
     */
    private $fakerDataProviderClasses;

    /**
     * A cache of instantiated column data providers
     *
     * @var array
     */
    private $fakerDataProviderObjects = [];
    /**
     * @var array
     */
    private $columnFormatters;
    /**
     * @var DoctrineStaticMeta
     */
    private $testedEntityDsm;
    /**
     * @var Faker\Guesser\Name
     */
    private $nameGuesser;
    /**
     * @var ColumnTypeGuesser
     */
    private $columnTypeGuesser;
    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;

    public function __construct(
        DoctrineStaticMeta $testedEntityDsm,
        NamespaceHelper $namespaceHelper,
        array $fakerDataProviderClasses,
        ?float $seed = null
    ) {
        $this->initFakerGenerator($seed);
        $this->testedEntityDsm          = $testedEntityDsm;
        $this->fakerDataProviderClasses = $fakerDataProviderClasses;
        $this->nameGuesser              = new \Faker\Guesser\Name(self::$generator);
        $this->columnTypeGuesser        = new ColumnTypeGuesser(self::$generator);
        $this->namespaceHelper          = $namespaceHelper;
        $this->checkFakerClassesRootNamespaceMatchesEntityFqn(
            $this->testedEntityDsm->getReflectionClass()->getName()
        );
        $this->generateColumnFormatters();
    }

    /**
     * @param float|null $seed
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function initFakerGenerator(?float $seed): void
    {
        if (null === self::$generator) {
            self::$generator = Faker\Factory::create();
            if (null !== $seed) {
                self::$generator->seed($seed);
            }
        }
    }

    private function checkFakerClassesRootNamespaceMatchesEntityFqn(string $fakedEntityFqn): void
    {
        if ([] === $this->fakerDataProviderClasses) {
            return;
        }
        $projectRootNamespace = null;
        foreach (array_keys($this->fakerDataProviderClasses) as $classField) {
            if (false === \ts\stringContains($classField, '-')) {
                continue;
            }
            list($entityFqn,) = explode('-', $classField);
            $rootNamespace = $this->namespaceHelper->getProjectNamespaceRootFromEntityFqn($entityFqn);
            if (null === $projectRootNamespace) {
                $projectRootNamespace = $rootNamespace;
                continue;
            }
            if ($rootNamespace !== $projectRootNamespace) {
                throw new \RuntimeException(
                    'Found unexpected root namespace ' .
                    $rootNamespace .
                    ', expecting ' .
                    $projectRootNamespace .
                    ', do we have mixed fakerProviderClasses? ' .
                    print_r($this->fakerDataProviderClasses, true)
                );
            }
        }
        if (null === $projectRootNamespace) {
            return;
        }
        $fakedEntityRootNamespace = $this->namespaceHelper->getProjectNamespaceRootFromEntityFqn($fakedEntityFqn);
        if ($fakedEntityRootNamespace === $projectRootNamespace) {
            return;
        }
        throw new \RuntimeException('Faked entity FQN ' .
                                    $fakedEntityFqn .
                                    ' project root namespace does not match the faker classes root namespace ' .
                                    $projectRootNamespace);

    }

    /**
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    private function generateColumnFormatters(): void
    {
        $entityFqn = $this->testedEntityDsm->getReflectionClass()->getName();
        $meta      = $this->testedEntityDsm->getMetaData();
        $this->guessColumnFormatters();
        $fieldNames = $meta->getFieldNames();

        foreach ($fieldNames as $fieldName) {
            if (isset($customFormatters[$fieldName])) {
                continue;
            }
            if (true === $this->addFakerDataProviderToColumnFormatters($fieldName, $entityFqn)) {
                continue;
            }
            $fieldMapping = $meta->getFieldMapping($fieldName);
            if (true === ($fieldMapping['unique'] ?? false)) {
                $this->addUniqueColumnFormatter($fieldMapping, $fieldName);
                continue;
            }
        }
    }

    private function guessColumnFormatters(): void
    {

        $meta = $this->testedEntityDsm->getMetaData();
        foreach ($meta->getFieldNames() as $fieldName) {
            if ($meta->isIdentifier($fieldName) || !$meta->hasField($fieldName)) {
                continue;
            }
            if (false !== \ts\stringContains($fieldName, '.')) {
                continue;
            }

            $size = $meta->fieldMappings[$fieldName]['length'] ?? null;
            if (null !== $formatter = $this->guessByName($fieldName, $size)) {
                $this->columnFormatters[$fieldName] = $formatter;
                continue;
            }
            if (null !== $formatter = $this->columnTypeGuesser->guessFormat($fieldName, $meta)) {
                $this->columnFormatters[$fieldName] = $formatter;
                continue;
            }
            if ('json' === $meta->fieldMappings[$fieldName]['type']) {
                $this->columnFormatters[$fieldName] = $this->getJson();
            }
        }
    }

    private function guessByName(string $fieldName, ?int $size): ?\Closure
    {
        $formatter = $this->nameGuesser->guessFormat($fieldName, $size);
        if (null !== $formatter) {
            return $formatter;
        }
        if (false !== \ts\stringContains($fieldName, 'email')) {
            return function () {
                return self::$generator->email;
            };
        }

        return null;
    }

    private function getJson(): string
    {
        $toEncode                     = [];
        $toEncode['string']           = self::$generator->text;
        $toEncode['float']            = self::$generator->randomFloat();
        $toEncode['nested']['string'] = self::$generator->text;
        $toEncode['nested']['float']  = self::$generator->randomFloat();

        return json_encode($toEncode, JSON_PRETTY_PRINT);
    }

    /**
     * Add a faker data provider to the columnFormatters array (by reference) if there is one available
     *
     * Handles instantiating and caching of the data providers
     *
     * @param string $fieldName
     *
     * @param string $entityFqn
     *
     * @return bool
     */
    private function addFakerDataProviderToColumnFormatters(
        string $fieldName,
        string $entityFqn
    ): bool {
        foreach ([
                     $entityFqn . '-' . $fieldName,
                     $fieldName,
                 ] as $key) {
            if (!isset($this->fakerDataProviderClasses[$key])) {
                continue;
            }
            if (!isset($this->fakerDataProviderObjects[$key])) {
                $class                                = $this->fakerDataProviderClasses[$key];
                $this->fakerDataProviderObjects[$key] = new $class(self::$generator);
            }
            $this->columnFormatters[$fieldName] = $this->fakerDataProviderObjects[$key];

            return true;
        }

        return false;
    }

    private function addUniqueColumnFormatter(array &$fieldMapping, string $fieldName): void
    {
        switch ($fieldMapping['type']) {
            case MappingHelper::TYPE_UUID:
                return;
            case MappingHelper::TYPE_STRING:
                $this->columnFormatters[$fieldName] = $this->getUniqueString();
                break;
            case MappingHelper::TYPE_INTEGER:
            case Type::BIGINT:
                $this->columnFormatters[$fieldName] = $this->getUniqueInt();
                break;
            default:
                throw new \InvalidArgumentException('unique field has an unsupported type: '
                                                    . print_r($fieldMapping, true));
        }
    }

    private function getUniqueString(): string
    {
        $string = 'unique string: ' . $this->getUniqueInt() . md5((string)time());
        while (isset(self::$uniqueStrings[$string])) {
            $string                       = md5((string)time());
            self::$uniqueStrings[$string] = true;
        }

        return $string;
    }

    private function getUniqueInt(): int
    {
        return ++self::$uniqueInt;
    }

    public function fillDtoFieldsWithData(DataTransferObjectInterface $dto): void
    {
        foreach ($this->columnFormatters as $field => $formatter) {
            if (null === $formatter) {
                continue;
            }
            try {
                $value  = \is_callable($formatter) ? $formatter($dto) : $formatter;
                $setter = 'set' . $field;
                $dto->$setter($value);
            } catch (\InvalidArgumentException $ex) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Failed to generate a value for %s::%s: %s',
                        \get_class($dto),
                        $field,
                        $ex->getMessage()
                    )
                );
            }
        }
    }
}