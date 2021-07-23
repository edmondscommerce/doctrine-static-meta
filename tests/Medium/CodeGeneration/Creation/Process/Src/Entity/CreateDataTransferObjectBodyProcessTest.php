<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Creation\Process\Src\Entity;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\DataTransferObjects\CreateDtoBodyProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

// phpcs:enable

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\DataTransferObjects\CreateDtoBodyProcess
 * @medium
 */
class CreateDataTransferObjectBodyProcessTest extends AbstractTest
{
    public const  WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/CreateDataTransferObjectBodyProcessTest';
    private const DTO      = <<<'PHP'
<?php declare(strict_types=1);
// phpcs:disable Generic.Files.LineLength.TooLong
namespace TemplateNamespace\Entity\DataTransferObjects;

use Doctrine\Common\Collections\ArrayCollection;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Debug\DebugEntityDataObjectIds;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entities\TemplateEntity;
use My\Test\Project\Entity\Interfaces\CompanyInterface;
use My\Test\Project\Entity\Interfaces\Large\RelationInterface;

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
final class TemplateEntityDto implements DataTransferObjectInterface
{

    /**
     * These are required imports that we have in this comment to prevent PHPStorm from removing them
     *
     * @see ArrayCollection
     * @see EntityInterface
     */

    use DebugEntityDataObjectIds;

    public const ENTITY_FQN = TemplateEntity::class;

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
        TemplateEntity::loadValidatorMetaData($metadata);
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


    /**
     * @var Collection<CompanyInterface>
     */
    private null|\Doctrine\Common\Collections\Collection $companies = null;

    /**
     * @var Collection<RelationInterface>
     */
    private null|\Doctrine\Common\Collections\Collection $largeRelations = null;

    /**
     * @var null|array<mixed>
     */
    private null|array $array = Director::DEFAULT_ARRAY;

    private null|\DateTimeImmutable $datetime = Director::DEFAULT_DATETIME;

    private null|\My\Test\Project\Entity\Interfaces\PersonInterface|\My\Test\Project\Entity\DataTransferObjects\PersonDto $person = null;

    private null|bool $boolean = Director::DEFAULT_BOOLEAN;

    private null|float $float = Director::DEFAULT_FLOAT;

    private null|int $integer = Director::DEFAULT_INTEGER;

    private null|object $object = Director::DEFAULT_OBJECT;

    private null|string $string = Director::DEFAULT_STRING;

    private null|string $text = Director::DEFAULT_TEXT;

    private string|int|float|null $decimal = Director::DEFAULT_DECIMAL;


    /**
     * @return Collection<CompanyInterface>
     */
    public function getCompanies(): null|\Doctrine\Common\Collections\Collection
    {
        return $this->companies ?? $this->companies = new ArrayCollection();
    }


    /**
     * @return Collection<RelationInterface>
     */
    public function getLargeRelations(): null|\Doctrine\Common\Collections\Collection
    {
        return $this->largeRelations ?? $this->largeRelations = new ArrayCollection();
    }


    /**
     * @return null|array<mixed>
     */
    public function getArray(): null|array
    {
        return $this->array;
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


    public function getPerson(): null|\My\Test\Project\Entity\Interfaces\PersonInterface
    {
        if(null === $this->person){
            return $this->person;
        }
        if($this->person instanceof \My\Test\Project\Entity\Interfaces\PersonInterface){
            return $this->person;
        }
        throw new \RuntimeException(
            '$this->person is not an Entity, but is '. $this->person::class
        );
    }


    public function getPersonDto(): null|\My\Test\Project\Entity\DataTransferObjects\PersonDto
    {
        if(null === $this->person){
            return $this->person;
        }
        if($this->person instanceof \My\Test\Project\Entity\DataTransferObjects\PersonDto){
            return $this->person;
        }
        throw new \RuntimeException(
            '$this->person is not a DTO, but is '. $this->person::class
        );
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


    public function issetPersonAsDto(): bool
    {
        return isset($this->person) && $this->person instanceof DataTransferObjectInterface;
    }


    public function issetPersonAsEntity(): bool
    {
       return isset($this->person) && $this->person instanceof EntityInterface;
    }


    /**
     * @param Collection<CompanyInterface> $companies
     */
    public function setCompanies(null|\Doctrine\Common\Collections\Collection $companies): self 
    {
        $this->companies = $companies;
        return $this;
    }


    /**
     * @param Collection<RelationInterface> $largeRelations
     */
    public function setLargeRelations(null|\Doctrine\Common\Collections\Collection $largeRelations): self 
    {
        $this->largeRelations = $largeRelations;
        return $this;
    }


    /**
     * @param null|array<mixed> $array
     */
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


    public function setPerson(null|\My\Test\Project\Entity\Interfaces\PersonInterface|\My\Test\Project\Entity\DataTransferObjects\PersonDto $person): self 
    {
        $this->person = $person;
        return $this;
    }


    public function setPersonDto(\My\Test\Project\Entity\DataTransferObjects\PersonDto $person): self 
    {
        $this->person = $person;
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

    protected static bool $buildOnce = true;

    public function setup(): void
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
    }

    /**
     * @test
     */
    public function itCanCreateTheDtoBodyForAnEntityWithFields(): void
    {
        $file = new File(
            __DIR__ . '/../../../../../../../codeTemplates/src/Entity/DataTransferObjects/TemplateEntityDto.php'
        );
        $file->loadContents();
        $entityFqn = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_DIRECTOR;
        $this->getProcess()
             ->setEntityFqn($entityFqn)
             ->run(new File\FindReplace($file));
        $expected = self::DTO;
        $actual   = $file->getContents();
        self::assertNotEmpty($actual);
        self::assertSame(trim($expected), trim($actual));
    }

    private function getProcess(): CreateDtoBodyProcess
    {
        $namespaceHelper = new NamespaceHelper();
        $typeHelper      = new TypeHelper();

        return new CreateDtoBodyProcess(
            new ReflectionHelper(
                $namespaceHelper,
                $typeHelper
            ),
            new CodeHelper(
                $namespaceHelper
            ),
            $namespaceHelper,
            $typeHelper
        );
    }
}
