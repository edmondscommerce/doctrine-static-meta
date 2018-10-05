<?php declare(strict_types=1);

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

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DtoCreator
 * @medium
 */
class DataTransferObjectCreatorTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/DataTransferObjectCreatorTest';
// phpcs:disable
    private const DTO = '<?php declare(strict_types=1);

namespace My\Test\Project\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Person;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This data transfer object should be used to hold unvalidated update data,
 * ready to be fed into the Entity::update method
 *
 * This class should never have any logic beyond getters and setters
 * @SuppressWarnings(PHPMD)
 */
final class PersonDto implements DataTransferObjectInterface
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
        Person::loadValidatorMetaData($metadata);
    }


    /**
     * @var ?string
     */
    private $string = Person::DEFAULT_STRING;

    /**
     * @var ?\DateTime
     */
    private $datetime = Person::DEFAULT_DATETIME;

    /**
     * @var ?float
     */
    private $float = Person::DEFAULT_FLOAT;

    /**
     */
    private $decimal = Person::DEFAULT_DECIMAL;

    /**
     * @var ?int
     */
    private $integer = Person::DEFAULT_INTEGER;

    /**
     * @var ?string
     */
    private $text = Person::DEFAULT_TEXT;

    /**
     * @var ?bool
     */
    private $boolean = Person::DEFAULT_BOOLEAN;

    /**
     * @var ?string
     */
    private $json = Person::DEFAULT_JSON;

    /**
     * @var ?\My\Test\Project\Entity\Interfaces\Attributes\AddressInterface
     */
    private $attributesAddress = null;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $attributesEmails = null;

    /**
     * @var ?\My\Test\Project\Entity\Interfaces\Company\DirectorInterface
     */
    private $companyDirector = null;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $orders = null;

    /**
     * @var ?\My\Test\Project\Entity\Interfaces\Large\RelationInterface
     */
    private $largeRelation = null;


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


    public function getAttributesAddress(): ?\My\Test\Project\Entity\Interfaces\Attributes\AddressInterface
    {
        return $this->attributesAddress;
    }


    public function getAttributesEmails(): \Doctrine\Common\Collections\Collection
    {
        return $this->attributesEmails ?? new ArrayCollection();
    }


    public function getCompanyDirector(): ?\My\Test\Project\Entity\Interfaces\Company\DirectorInterface
    {
        return $this->companyDirector;
    }


    public function getOrders(): \Doctrine\Common\Collections\Collection
    {
        return $this->orders ?? new ArrayCollection();
    }


    public function getLargeRelation(): ?\My\Test\Project\Entity\Interfaces\Large\RelationInterface
    {
        return $this->largeRelation;
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


    public function setAttributesAddress(?\My\Test\Project\Entity\Interfaces\Attributes\AddressInterface $attributesAddress): self 
    {
        $this->attributesAddress = $attributesAddress;
        return $this;
    }


    public function setAttributesEmails(\Doctrine\Common\Collections\Collection $attributesEmails): self 
    {
        $this->attributesEmails = $attributesEmails;
        return $this;
    }


    public function setCompanyDirector(?\My\Test\Project\Entity\Interfaces\Company\DirectorInterface $companyDirector): self 
    {
        $this->companyDirector = $companyDirector;
        return $this;
    }


    public function setOrders(\Doctrine\Common\Collections\Collection $orders): self 
    {
        $this->orders = $orders;
        return $this;
    }


    public function setLargeRelation(?\My\Test\Project\Entity\Interfaces\Large\RelationInterface $largeRelation): self 
    {
        $this->largeRelation = $largeRelation;
        return $this;
    }

}';

    private const NESTED_DTO = '<?php declare(strict_types=1);

namespace My\Test\Project\Entity\DataTransferObjects\Another\Deeply\Nested;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Another\Deeply\Nested\Client;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This data transfer object should be used to hold unvalidated update data,
 * ready to be fed into the Entity::update method
 *
 * This class should never have any logic beyond getters and setters
 * @SuppressWarnings(PHPMD)
 */
final class ClientDto implements DataTransferObjectInterface
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
        Client::loadValidatorMetaData($metadata);
    }


    /**
     * @var ?string
     */
    private $string = Client::DEFAULT_STRING;

    /**
     * @var ?\DateTime
     */
    private $datetime = Client::DEFAULT_DATETIME;

    /**
     * @var ?float
     */
    private $float = Client::DEFAULT_FLOAT;

    /**
     */
    private $decimal = Client::DEFAULT_DECIMAL;

    /**
     * @var ?int
     */
    private $integer = Client::DEFAULT_INTEGER;

    /**
     * @var ?string
     */
    private $text = Client::DEFAULT_TEXT;

    /**
     * @var ?bool
     */
    private $boolean = Client::DEFAULT_BOOLEAN;

    /**
     * @var ?string
     */
    private $json = Client::DEFAULT_JSON;

    /**
     * @var ?\My\Test\Project\Entity\Interfaces\CompanyInterface
     */
    private $company = null;


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


    public function getCompany(): ?\My\Test\Project\Entity\Interfaces\CompanyInterface
    {
        return $this->company;
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


    public function setCompany(?\My\Test\Project\Entity\Interfaces\CompanyInterface $company): self 
    {
        $this->company = $company;
        return $this;
    }

}';
// phpcs:enable
    protected static $buildOnce = true;

    public function setup()
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
    public function itCanCreateADto()
    {
        $newObjectFqn = $this->getCopiedFqn(
            self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\DataTransferObjects\\PersonDto'
        );
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::DTO;
        $actual       = $this->replaceNamespaceBackToStandard($file);
        self::assertSame($expected, $actual);
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
        return \str_replace(
            ltrim($this->getCopiedFqn('My\\Test\\Project'), '\\'),
            'My\\Test\\Project\\',
            $file->getContents()
        );
    }

    /**
     * @test
     */
    public function itCanCreateADtoFromAnEntityFqn()
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
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANestedDto()
    {
        $newObjectFqn = $this->getCopiedFqn(
            self::TEST_PROJECT_ROOT_NAMESPACE .
            '\\Entity\\DataTransferObjects\\Another\\Deeply\\Nested\\ClientDto'
        );
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::NESTED_DTO;
        $actual       = $this->replaceNamespaceBackToStandard($file);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANestedDtoFromEntityFqn()
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
        self::assertSame($expected, $actual);
    }
}
