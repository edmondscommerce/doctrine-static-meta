<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Creation\Src\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DtoCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;

use function str_replace;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DtoCreator
 * @medium
 */
class DataTransferObjectCreatorTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/DataTransferObjectCreatorTest';
// phpcs:disable
    private const DTO = <<<'PHP'
<?php declare(strict_types=1);
// phpcs:disable Generic.Files.LineLength.TooLong
namespace My\Test\Project\Entity\DataTransferObjects;

use Doctrine\Common\Collections\ArrayCollection;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Debug\DebugEntityDataObjectIds;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Person;

/**
 * This data transfer object should be used to hold potentially unvalidated update data,
 * ready to be fed into the Entity::update method
 *
 * You can choose to validate the DTO, but it the Entity will still be validated at the Entity::update stage
 *
 * Entity Properties use a single class property which can be either
 * - DataTransferObjectInterface
 * - EntityInterface
 *
 * This class should never have any logic beyond getters and setters
 * @SuppressWarnings(PHPMD)
 */
final class PersonDto implements DataTransferObjectInterface
{

    /**
     * These are required imports that we have in this comment to prevent PHPStorm from removing them
     *
     * @see ArrayCollection
     * @see EntityInterface
     */

    use DebugEntityDataObjectIds;

    public const ENTITY_FQN = Person::class;

    private UuidInterface $id;

    /**
     * This method is called by the Symfony validation component when loading the meta data
     *
     * In this method, we pass the meta data through to the Entity so that it can be configured
     *
     * @throws DoctrineStaticMetaException
     */
    public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void
    {
        Person::loadValidatorMetaData($metadata);
    }

    public static function getEntityFqn(): string
    {
        return self::ENTITY_FQN;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        $this->initDebugIds(true);

        return $this;
    }


    private null|\My\Test\Project\Entity\Interfaces\Attributes\AddressInterface $attributesAddress = null;

    private null|\My\Test\Project\Entity\Interfaces\Company\DirectorInterface $companyDirector = null;

    private null|\My\Test\Project\Entity\Interfaces\Large\RelationInterface $largeRelation = null;

    private null|\DateTimeImmutable $datetime = Person::DEFAULT_DATETIME;

    private null|\Doctrine\Common\Collections\Collection $attributesEmails = null;

    private null|array $array = Person::DEFAULT_ARRAY;

    private null|bool $boolean = Person::DEFAULT_BOOLEAN;

    private null|float $float = Person::DEFAULT_FLOAT;

    private null|int $integer = Person::DEFAULT_INTEGER;

    private null|object $object = Person::DEFAULT_OBJECT;

    private null|string $string = Person::DEFAULT_STRING;

    private null|string $text = Person::DEFAULT_TEXT;

    private string|int|float|null $decimal = Person::DEFAULT_DECIMAL;


    public function getArray(): null|array
    {
        return $this->array;
    }


    public function getAttributesAddress(): null|\My\Test\Project\Entity\Interfaces\Attributes\AddressInterface
    {
        if(null === $this->attributesAddress){
            return $this->attributesAddress;
        }
        if($this->attributesAddress instanceof \My\Test\Project\Entity\Interfaces\Attributes\AddressInterface){
            return $this->attributesAddress;
        }
        throw new \RuntimeException(
            '$this->attributesAddress is not an Entity, but is '. \get_class($this->attributesAddress)
        );
    }


    public function getAttributesAddressDto(): null|\My\Test\Project\Entity\DataTransferObjects\Attributes\AddressDto
    {
        if(null === $this->attributesAddress){
            return $this->attributesAddress;
        }
        if($this->attributesAddress instanceof null|\My\Test\Project\Entity\DataTransferObjects\Attributes\AddressDto){
            return $this->attributesAddress;
        }
        throw new \RuntimeException(
            '$this->attributesAddress is not a DTO, but is '. \get_class($this->attributesAddress)
        );
    }


    public function getAttributesEmails(): null|\Doctrine\Common\Collections\Collection
    {
        return $this->attributesEmails;
    }


    public function getCompanyDirector(): null|\My\Test\Project\Entity\Interfaces\Company\DirectorInterface
    {
        if(null === $this->companyDirector){
            return $this->companyDirector;
        }
        if($this->companyDirector instanceof \My\Test\Project\Entity\Interfaces\Company\DirectorInterface){
            return $this->companyDirector;
        }
        throw new \RuntimeException(
            '$this->companyDirector is not an Entity, but is '. \get_class($this->companyDirector)
        );
    }


    public function getCompanyDirectorDto(): null|\My\Test\Project\Entity\DataTransferObjects\Company\DirectorDto
    {
        if(null === $this->companyDirector){
            return $this->companyDirector;
        }
        if($this->companyDirector instanceof null|\My\Test\Project\Entity\DataTransferObjects\Company\DirectorDto){
            return $this->companyDirector;
        }
        throw new \RuntimeException(
            '$this->companyDirector is not a DTO, but is '. \get_class($this->companyDirector)
        );
    }


    public function getDatetime(): null|\DateTimeImmutable
    {
        return $this->datetime;
    }


    public function getDecimal(): string|int|float|null
    {
        return $this->decimal;
    }


    public function getFloat(): null|float
    {
        return $this->float;
    }


    public function getInteger(): null|int
    {
        return $this->integer;
    }


    public function getLargeRelation(): null|\My\Test\Project\Entity\Interfaces\Large\RelationInterface
    {
        if(null === $this->largeRelation){
            return $this->largeRelation;
        }
        if($this->largeRelation instanceof \My\Test\Project\Entity\Interfaces\Large\RelationInterface){
            return $this->largeRelation;
        }
        throw new \RuntimeException(
            '$this->largeRelation is not an Entity, but is '. \get_class($this->largeRelation)
        );
    }


    public function getLargeRelationDto(): null|\My\Test\Project\Entity\DataTransferObjects\Large\RelationDto
    {
        if(null === $this->largeRelation){
            return $this->largeRelation;
        }
        if($this->largeRelation instanceof null|\My\Test\Project\Entity\DataTransferObjects\Large\RelationDto){
            return $this->largeRelation;
        }
        throw new \RuntimeException(
            '$this->largeRelation is not a DTO, but is '. \get_class($this->largeRelation)
        );
    }


    public function getObject(): null|object
    {
        return $this->object;
    }


    public function getString(): null|string
    {
        return $this->string;
    }


    public function getText(): null|string
    {
        return $this->text;
    }


    public function isBoolean(): null|bool
    {
        return $this->boolean;
    }


    public function issetAttributesAddressAsDto(): bool
    {
        return isset($this->attributesAddress) && $this->attributesAddress instanceof DataTransferObjectInterface;
    }


    public function issetAttributesAddressAsEntity(): bool
    {
       return isset($this->attributesAddress) && $this->attributesAddress instanceof EntityInterface;
    }


    public function issetCompanyDirectorAsDto(): bool
    {
        return isset($this->companyDirector) && $this->companyDirector instanceof DataTransferObjectInterface;
    }


    public function issetCompanyDirectorAsEntity(): bool
    {
       return isset($this->companyDirector) && $this->companyDirector instanceof EntityInterface;
    }


    public function issetLargeRelationAsDto(): bool
    {
        return isset($this->largeRelation) && $this->largeRelation instanceof DataTransferObjectInterface;
    }


    public function issetLargeRelationAsEntity(): bool
    {
       return isset($this->largeRelation) && $this->largeRelation instanceof EntityInterface;
    }


    public function setArray(null|array $array): self 
    {
        $this->array = $array;
        return $this;
    }


    public function setAttributesAddress(null|\My\Test\Project\Entity\Interfaces\Attributes\AddressInterface $attributesAddress): self 
    {
        $this->attributesAddress = $attributesAddress;
        return $this;
    }


    public function setAttributesAddressDto(null|\My\Test\Project\Entity\DataTransferObjects\Attributes\AddressDto $attributesAddress): self 
    {
        $this->attributesAddress = $attributesAddress;
        return $this;
    }


    public function setAttributesEmails(null|\Doctrine\Common\Collections\Collection $attributesEmails): self 
    {
        $this->attributesEmails = $attributesEmails;
        return $this;
    }


    public function setBoolean(null|bool $boolean): self 
    {
        $this->boolean = $boolean;
        return $this;
    }


    public function setCompanyDirector(null|\My\Test\Project\Entity\Interfaces\Company\DirectorInterface $companyDirector): self 
    {
        $this->companyDirector = $companyDirector;
        return $this;
    }


    public function setCompanyDirectorDto(null|\My\Test\Project\Entity\DataTransferObjects\Company\DirectorDto $companyDirector): self 
    {
        $this->companyDirector = $companyDirector;
        return $this;
    }


    public function setDatetime(null|\DateTimeImmutable $datetime): self 
    {
        $this->datetime = $datetime;
        return $this;
    }


    public function setDecimal(string|int|float|null $decimal): self 
    {
        $this->decimal = $decimal;
        return $this;
    }


    public function setFloat(null|float $float): self 
    {
        $this->float = $float;
        return $this;
    }


    public function setInteger(null|int $integer): self 
    {
        $this->integer = $integer;
        return $this;
    }


    public function setLargeRelation(null|\My\Test\Project\Entity\Interfaces\Large\RelationInterface $largeRelation): self 
    {
        $this->largeRelation = $largeRelation;
        return $this;
    }


    public function setLargeRelationDto(null|\My\Test\Project\Entity\DataTransferObjects\Large\RelationDto $largeRelation): self 
    {
        $this->largeRelation = $largeRelation;
        return $this;
    }


    public function setObject(null|object $object): self 
    {
        $this->object = $object;
        return $this;
    }


    public function setString(null|string $string): self 
    {
        $this->string = $string;
        return $this;
    }


    public function setText(null|string $text): self 
    {
        $this->text = $text;
        return $this;
    }

}

PHP;

    private const NESTED_DTO = <<<'PHP'
<?php declare(strict_types=1);
// phpcs:disable Generic.Files.LineLength.TooLong
namespace My\Test\Project\Entity\DataTransferObjects\Another\Deeply\Nested;

use Doctrine\Common\Collections\ArrayCollection;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Debug\DebugEntityDataObjectIds;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Another\Deeply\Nested\Client;

/**
 * This data transfer object should be used to hold potentially unvalidated update data,
 * ready to be fed into the Entity::update method
 *
 * You can choose to validate the DTO, but it the Entity will still be validated at the Entity::update stage
 *
 * Entity Properties use a single class property which can be either
 * - DataTransferObjectInterface
 * - EntityInterface
 *
 * This class should never have any logic beyond getters and setters
 * @SuppressWarnings(PHPMD)
 */
final class ClientDto implements DataTransferObjectInterface
{

    /**
     * These are required imports that we have in this comment to prevent PHPStorm from removing them
     *
     * @see ArrayCollection
     * @see EntityInterface
     */

    use DebugEntityDataObjectIds;

    public const ENTITY_FQN = Client::class;

    private UuidInterface $id;

    /**
     * This method is called by the Symfony validation component when loading the meta data
     *
     * In this method, we pass the meta data through to the Entity so that it can be configured
     *
     * @throws DoctrineStaticMetaException
     */
    public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void
    {
        Client::loadValidatorMetaData($metadata);
    }

    public static function getEntityFqn(): string
    {
        return self::ENTITY_FQN;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        $this->initDebugIds(true);

        return $this;
    }


    private null|\My\Test\Project\Entity\Interfaces\CompanyInterface $company = null;

    private null|\DateTimeImmutable $datetime = Client::DEFAULT_DATETIME;

    private null|array $array = Client::DEFAULT_ARRAY;

    private null|bool $boolean = Client::DEFAULT_BOOLEAN;

    private null|float $float = Client::DEFAULT_FLOAT;

    private null|int $integer = Client::DEFAULT_INTEGER;

    private null|object $object = Client::DEFAULT_OBJECT;

    private null|string $string = Client::DEFAULT_STRING;

    private null|string $text = Client::DEFAULT_TEXT;

    private string|int|float|null $decimal = Client::DEFAULT_DECIMAL;


    public function getArray(): null|array
    {
        return $this->array;
    }


    public function getCompany(): null|\My\Test\Project\Entity\Interfaces\CompanyInterface
    {
        if(null === $this->company){
            return $this->company;
        }
        if($this->company instanceof \My\Test\Project\Entity\Interfaces\CompanyInterface){
            return $this->company;
        }
        throw new \RuntimeException(
            '$this->company is not an Entity, but is '. \get_class($this->company)
        );
    }


    public function getCompanyDto(): null|\My\Test\Project\Entity\DataTransferObjects\CompanyDto
    {
        if(null === $this->company){
            return $this->company;
        }
        if($this->company instanceof null|\My\Test\Project\Entity\DataTransferObjects\CompanyDto){
            return $this->company;
        }
        throw new \RuntimeException(
            '$this->company is not a DTO, but is '. \get_class($this->company)
        );
    }


    public function getDatetime(): null|\DateTimeImmutable
    {
        return $this->datetime;
    }


    public function getDecimal(): string|int|float|null
    {
        return $this->decimal;
    }


    public function getFloat(): null|float
    {
        return $this->float;
    }


    public function getInteger(): null|int
    {
        return $this->integer;
    }


    public function getObject(): null|object
    {
        return $this->object;
    }


    public function getString(): null|string
    {
        return $this->string;
    }


    public function getText(): null|string
    {
        return $this->text;
    }


    public function isBoolean(): null|bool
    {
        return $this->boolean;
    }


    public function issetCompanyAsDto(): bool
    {
        return isset($this->company) && $this->company instanceof DataTransferObjectInterface;
    }


    public function issetCompanyAsEntity(): bool
    {
       return isset($this->company) && $this->company instanceof EntityInterface;
    }


    public function setArray(null|array $array): self 
    {
        $this->array = $array;
        return $this;
    }


    public function setBoolean(null|bool $boolean): self 
    {
        $this->boolean = $boolean;
        return $this;
    }


    public function setCompany(null|\My\Test\Project\Entity\Interfaces\CompanyInterface $company): self 
    {
        $this->company = $company;
        return $this;
    }


    public function setCompanyDto(null|\My\Test\Project\Entity\DataTransferObjects\CompanyDto $company): self 
    {
        $this->company = $company;
        return $this;
    }


    public function setDatetime(null|\DateTimeImmutable $datetime): self 
    {
        $this->datetime = $datetime;
        return $this;
    }


    public function setDecimal(string|int|float|null $decimal): self 
    {
        $this->decimal = $decimal;
        return $this;
    }


    public function setFloat(null|float $float): self 
    {
        $this->float = $float;
        return $this;
    }


    public function setInteger(null|int $integer): self 
    {
        $this->integer = $integer;
        return $this;
    }


    public function setObject(null|object $object): self 
    {
        $this->object = $object;
        return $this;
    }


    public function setString(null|string $string): self 
    {
        $this->string = $string;
        return $this;
    }


    public function setText(null|string $text): self 
    {
        $this->text = $text;
        return $this;
    }

}

PHP;

// phpcs:enable
    protected static bool $buildOnce = true;

    public function setup():void
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
    }

    /**
     * @test
     */
    public function itCanCreateADto(): void
    {
        $newObjectFqn = $this->getCopiedFqn(
            self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\DataTransferObjects\\PersonDto'
        );
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::DTO;
        $actual       = $this->replaceNamespaceBackToStandard($file);
        self::assertSame(trim($expected), trim($actual));
    }

    private function getCreator(): DtoCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        $creator = new DtoCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory(),
            new ReflectionHelper($namespaceHelper),
            new CodeHelper($namespaceHelper)
        );
        $creator->setProjectRootNamespace(ltrim($this->getCopiedFqn(self::TEST_PROJECT_ROOT_NAMESPACE), '\\'));
        $creator->setProjectRootDirectory($this->copiedWorkDir);

        return $creator;
    }

    private function replaceNamespaceBackToStandard(File $file): string
    {
        return str_replace(
            ltrim($this->getCopiedFqn('My\\Test\\Project'), '\\'),
            'My\\Test\\Project\\',
            $file->getContents()
        );
    }

    /**
     * @test
     */
    public function itCanCreateADtoFromAnEntityFqn(): void
    {
        $entityFqn = $this->getCopiedFqn(
            self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Person'
        );
        $file      = $this->getCreator()
                          ->setNewObjectFqnFromEntityFqn($entityFqn)
                          ->createTargetFileObject()
                          ->getTargetFile();
        $expected  = self::DTO;
        $actual    = $this->replaceNamespaceBackToStandard($file);
        self::assertSame(trim($expected), trim($actual));
    }

    /**
     * @test
     */
    public function itCanCreateANestedDto(): void
    {
        $newObjectFqn = $this->getCopiedFqn(
            self::TEST_PROJECT_ROOT_NAMESPACE .
            '\\Entity\\DataTransferObjects\\Another\\Deeply\\Nested\\ClientDto'
        );
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::NESTED_DTO;
        $actual       = $this->replaceNamespaceBackToStandard($file);
        self::assertSame(trim($expected), trim($actual));
    }

    /**
     * @test
     */
    public function itCanCreateANestedDtoFromEntityFqn(): void
    {
        $entityFqn = $this->getCopiedFqn(
            self::TEST_PROJECT_ROOT_NAMESPACE .
            '\\Entities\\Another\\Deeply\\Nested\\Client'
        );
        $file      = $this->getCreator()
                          ->setNewObjectFqnFromEntityFqn($entityFqn)
                          ->createTargetFileObject()
                          ->getTargetFile();
        $expected  = self::NESTED_DTO;
        $actual    = $this->replaceNamespaceBackToStandard($file);
        self::assertSame(trim($expected), trim($actual));
    }
}
