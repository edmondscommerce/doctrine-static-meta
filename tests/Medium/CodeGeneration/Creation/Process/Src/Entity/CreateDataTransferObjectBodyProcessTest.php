<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Creation\Process\Src\Entity;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\CreateDtoBodyProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\CreateDtoBodyProcess
 * @medium
 */
class CreateDataTransferObjectBodyProcessTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/CreateDataTransferObjectBodyProcessTest';
    private const DTO = '<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entities\TemplateEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This data transfer object should be used to hold unvalidated update data,
 * ready to be fed into the Entity::update method
 *
 * This class should never have any logic beyond getters and setters
 */
final class TemplateEntityDto implements DataTransferObjectInterface
{

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


    /**
     * @var ?string
     */
    private $string = Director::DEFAULT_STRING;

    /**
     * @var ?\DateTime
     */
    private $datetime = Director::DEFAULT_DATETIME;

    /**
     * @var ?float
     */
    private $float = Director::DEFAULT_FLOAT;

    /**
     */
    private $decimal = Director::DEFAULT_DECIMAL;

    /**
     * @var ?int
     */
    private $integer = Director::DEFAULT_INTEGER;

    /**
     * @var ?string
     */
    private $text = Director::DEFAULT_TEXT;

    /**
     * @var ?bool
     */
    private $boolean = Director::DEFAULT_BOOLEAN;

    /**
     * @var ?string
     */
    private $json = Director::DEFAULT_JSON;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $companies = null;

    /**
     * @var ?\My\Test\Project\Entity\Interfaces\PersonInterface
     */
    private $person = null;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $largeRelations = null;


    public function getString(): ?string
    {
        return $this->string;
    }


    public function getDatetime(): ?\DateTime
    {
        return $this->datetime;
    }


    public function getFloat(): ?float
    {
        return $this->float;
    }


    public function getDecimal()
    {
        return $this->decimal;
    }


    public function getInteger(): ?int
    {
        return $this->integer;
    }


    public function getText(): ?string
    {
        return $this->text;
    }


    public function isBoolean(): ?bool
    {
        return $this->boolean;
    }


    public function getJson(): ?string
    {
        return $this->json;
    }


    public function getCompanies(): \Doctrine\Common\Collections\Collection
    {
        return $this->companies ?? new ArrayCollection();
    }


    public function getPerson(): ?\My\Test\Project\Entity\Interfaces\PersonInterface
    {
        return $this->person;
    }


    public function getLargeRelations(): \Doctrine\Common\Collections\Collection
    {
        return $this->largeRelations ?? new ArrayCollection();
    }


    public function setString(?string $string): self 
    {
        $this->string = $string;
        return $this;
    }


    public function setDatetime(?\DateTime $datetime): self 
    {
        $this->datetime = $datetime;
        return $this;
    }


    public function setFloat(?float $float): self 
    {
        $this->float = $float;
        return $this;
    }


    public function setDecimal( $decimal): self 
    {
        $this->decimal = $decimal;
        return $this;
    }


    public function setInteger(?int $integer): self 
    {
        $this->integer = $integer;
        return $this;
    }


    public function setText(?string $text): self 
    {
        $this->text = $text;
        return $this;
    }


    public function setBoolean(?bool $boolean): self 
    {
        $this->boolean = $boolean;
        return $this;
    }


    public function setJson(?string $json): self 
    {
        $this->json = $json;
        return $this;
    }


    public function setCompanies(\Doctrine\Common\Collections\Collection $companies): self 
    {
        $this->companies = $companies;
        return $this;
    }


    public function setPerson(?\My\Test\Project\Entity\Interfaces\PersonInterface $person): self 
    {
        $this->person = $person;
        return $this;
    }


    public function setLargeRelations(\Doctrine\Common\Collections\Collection $largeRelations): self 
    {
        $this->largeRelations = $largeRelations;
        return $this;
    }

}';
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
    public function itCanCreateTheDtoBodyForAnEntityWithFields()
    {
        $file = new File(
            __DIR__ . '/../../../../../../../codeTemplates/src/Entity/DataTransferObjects/TemplateEntityDto.php'
        );
        $file->loadContents();
        $entityFqn = self::TEST_ENTITIES_ROOT_NAMESPACE . '\\Company\\Director';
        $this->getProcess()
             ->setEntityFqn($entityFqn)
             ->run(new File\FindReplace($file));
        $expected = self::DTO;
        $actual   = $file->getContents();
        self::assertNotEmpty($actual);
        self::assertSame($expected, $actual);
    }

    private function getProcess(): CreateDtoBodyProcess
    {
        return new CreateDtoBodyProcess(
            new ReflectionHelper(
                new NamespaceHelper()
            ),
            new CodeHelper(
                new NamespaceHelper()
            )
        );
    }
}
