<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Factory;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityDependencyInjector
 */
class EntityDependencyInjectorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/EntityDependencyInjectorTest/';

    private const TEST_ENTITY_FILE = '/src/Entities/Order.php';
    private const TEST_ENTITY_FQN  = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ORDER;

    protected static $buildOnce = true;

    /**
     * @var string
     */
    private $entityFqn;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            $this->overrideOrderEntity();
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY_FQN);
    }

    private function overrideOrderEntity(): void
    {
        \ts\file_put_contents(
            self::WORK_DIR . self::TEST_ENTITY_FILE,
            str_replace(
                ';
}
',
                <<<'TEXT'
;
    private static $namespaceHelper;
    private $filesystem;

	public function injectFilesystem(\Symfony\Component\Filesystem\Filesystem $filesystem): void{
	    $this->filesystem=$filesystem;
	}
	
	public function getFilesystem():\Symfony\Component\Filesystem\Filesystem{
	    return $this->filesystem;
	}
	
	public static function injectNamespaceHelper(\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper $namespaceHelper){
	    self::$namespaceHelper=$namespaceHelper;
	}
	
	public static function getNamespaceHelper():\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper{
	    return self::$namespaceHelper;
	}
	
}

TEXT
                ,
                \ts\file_get_contents(self::WORK_DIR . self::TEST_ENTITY_FILE)
            )
        );
    }

    /**
     * @test
     * @large
     */
    public function itCanInjectDependencies(): void
    {
        $entity = $this->createOrderEntity();
        self::assertInstanceOf(Filesystem::class, $entity->getFilesystem());
        self::assertInstanceOf(NamespaceHelper::class, $entity::getNamespaceHelper());
    }

    /**
     * @return \EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface|mixed
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    private function createOrderEntity()
    {
        $emailFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_EMAIL
        );
        $emailDto = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn($emailFqn)
                         ->setEmailAddress('person@mail.com');

        $personFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON
        );
        $personDto = $this->getEntityDtoFactory()
                          ->createEmptyDtoFromEntityFqn($personFqn);
        $personDto->getAttributesEmails()->add($emailDto);

        $addressFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ATTRIBUTES_ADDRESS
        );
        $addressDto = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn($addressFqn);

        $orderAddressFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ORDER_ADDRESS
        );
        $orderAddressDto = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn($orderAddressFqn);
        $orderAddressDto->setAttributesAddressDto($addressDto);

        $orderDto = $this->getEntityDtoFactory()
                         ->createEmptyDtoFromEntityFqn($this->entityFqn)
                         ->setPersonDto(
                             $personDto
                         );
        $orderDto->getOrderAddresses()->add($orderAddressDto);

        return $this->createEntity(
            $this->entityFqn,
            $orderDto
        );
    }

    /*****************************************************************************************
     * Uncomment the below tests when we have implemented BetterReflection
     * and can stop having to deal with previously loaded code issues
     ****************************************************************************************/

//    /**
//     * @test
//     * @large
//     */
//    public function itThrowsAnExceptionIfAnInjectMethodDoesNotHaveOnlyOneParam(): void
//    {
//        \ts\file_put_contents(
//            $this->copiedWorkDir . self::TEST_ENTITY_FILE,
//            \str_replace(
//                'public static function injectNamespaceHelper(NamespaceHelper $namespaceHelper){',
//                'public static function injectNamespaceHelper(NamespaceHelper $namespaceHelper, bool $thing){',
//                \ts\file_get_contents($this->copiedWorkDir . self::TEST_ENTITY_FILE)
//            )
//        );
//        $this->expectException(\RuntimeException::class);
//        $this->expectExceptionMessage(
//            'Invalid method signature for injectNamespaceHelper, ' .
//            'should only take one argument which is the dependency to be injected'
//        );
//        $this->createOrderEntity();
//    }
//
//    /**
//     * @test
//     * @large
//     */
//    public function itThrowsAnExceptionIfAnInjectMethodHasNoTypeHint(): void
//    {
//        \ts\file_put_contents(
//            $this->copiedWorkDir . self::TEST_ENTITY_FILE,
//            \str_replace(
//                'public static function injectNamespaceHelper(NamespaceHelper $namespaceHelper){',
//                'public static function injectNamespaceHelper($namespaceHelper){',
//                \ts\file_get_contents($this->copiedWorkDir . self::TEST_ENTITY_FILE)
//            )
//        );
//
//        $this->expectException(\RuntimeException::class);
//        $this->expectExceptionMessage(
//            'Invalid method signature for injectNamespaceHelper, the object being set must be type hinted'
//        );
//        $this->createOrderEntity();
//    }
}
