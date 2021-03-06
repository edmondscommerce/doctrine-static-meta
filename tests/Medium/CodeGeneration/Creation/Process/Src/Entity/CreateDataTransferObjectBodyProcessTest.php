<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Creation\Process\Src\Entity;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\DataTransferObjects\CreateDtoBodyProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
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

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * This method is called by the Symfony validation component when loading the meta data
     *
     * In this method, we pass the meta data through to the Entity so that it can be configured
     *
     * @param ValidatorClassMetaData $metadata
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
     * @var ?\DateTimeImmutable
     */
    private $datetime = Director::DEFAULT_DATETIME;

    /**
     * @var ?array
     */
    private $array = Director::DEFAULT_ARRAY;

    /**
     * @var ?bool
     */
    private $boolean = Director::DEFAULT_BOOLEAN;

    /**
     * @var ?float
     */
    private $float = Director::DEFAULT_FLOAT;

    /**
     * @var ?int
     */
    private $integer = Director::DEFAULT_INTEGER;

    /**
     * @var ?object
     */
    private $object = Director::DEFAULT_OBJECT;

    /**
     * @var ?string
     */
    private $string = Director::DEFAULT_STRING;

    /**
     * @var ?string
     */
    private $text = Director::DEFAULT_TEXT;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $companies = null;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $largeRelations = null;

    /**
     * @var \My\Test\Project\Entity\Interfaces\PersonInterface|\My\Test\Project\Entity\DataTransferObjects\PersonDto
     */
    private $person = null;

    /**
     */
    private $decimal = Director::DEFAULT_DECIMAL;


    public function getArray(): ?array
    {
        return $this->array;
    }


    public function getCompanies(): \Doctrine\Common\Collections\Collection
    {
        return $this->companies ?? $this->companies = new ArrayCollection();
    }


    public function getDatetime(): ?\DateTimeImmutable
    {
        return $this->datetime;
    }


    public function getDecimal()
    {
        return $this->decimal;
    }


    public function getFloat(): ?float
    {
        return $this->float;
    }


    public function getInteger(): ?int
    {
        return $this->integer;
    }


    public function getLargeRelations(): \Doctrine\Common\Collections\Collection
    {
        return $this->largeRelations ?? $this->largeRelations = new ArrayCollection();
    }


    public function getObject(): ?object
    {
        return $this->object;
    }


    public function getPerson(): \My\Test\Project\Entity\Interfaces\PersonInterface
    {
        if(null === $this->person){
            return $this->person;
        }
        if($this->person instanceof \My\Test\Project\Entity\Interfaces\PersonInterface){
            return $this->person;
        }
        throw new \RuntimeException(
            '$this->person is not an Entity, but is '. \get_class($this->person)
        );
    }


    public function getPersonDto(): \My\Test\Project\Entity\DataTransferObjects\PersonDto
    {
        if(null === $this->person){
            return $this->person;
        }
        if($this->person instanceof \My\Test\Project\Entity\DataTransferObjects\PersonDto){
            return $this->person;
        }
        throw new \RuntimeException(
            '$this->person is not a DTO, but is '. \get_class($this->person)
        );
    }


    public function getString(): ?string
    {
        return $this->string;
    }


    public function getText(): ?string
    {
        return $this->text;
    }


    public function isBoolean(): ?bool
    {
        return $this->boolean;
    }


    public function issetPersonAsDto(): bool
    {
        return $this->person instanceof DataTransferObjectInterface;
    }


    public function issetPersonAsEntity(): bool
    {
        return $this->person instanceof EntityInterface;
    }


    public function setArray(?array $array): self 
    {
        $this->array = $array;
        return $this;
    }


    public function setBoolean(?bool $boolean): self 
    {
        $this->boolean = $boolean;
        return $this;
    }


    public function setCompanies(\Doctrine\Common\Collections\Collection $companies): self 
    {
        $this->companies = $companies;
        return $this;
    }


    public function setDatetime(?\DateTimeImmutable $datetime): self 
    {
        $this->datetime = $datetime;
        return $this;
    }


    public function setDecimal( $decimal): self 
    {
        $this->decimal = $decimal;
        return $this;
    }


    public function setFloat(?float $float): self 
    {
        $this->float = $float;
        return $this;
    }


    public function setInteger(?int $integer): self 
    {
        $this->integer = $integer;
        return $this;
    }


    public function setLargeRelations(\Doctrine\Common\Collections\Collection $largeRelations): self 
    {
        $this->largeRelations = $largeRelations;
        return $this;
    }


    public function setObject(?object $object): self 
    {
        $this->object = $object;
        return $this;
    }


    public function setPerson(\My\Test\Project\Entity\Interfaces\PersonInterface $person): self 
    {
        $this->person = $person;
        return $this;
    }


    public function setPersonDto(\My\Test\Project\Entity\DataTransferObjects\PersonDto $person): self 
    {
        $this->person = $person;
        return $this;
    }


    public function setString(?string $string): self 
    {
        $this->string = $string;
        return $this;
    }


    public function setText(?string $text): self 
    {
        $this->text = $text;
        return $this;
    }

}
PHP;

    protected static $buildOnce = true;

    public function setup()
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

        return new CreateDtoBodyProcess(
            new ReflectionHelper(
                $namespaceHelper
            ),
            new CodeHelper(
                $namespaceHelper
            ),
            $namespaceHelper
        );
    }
}
