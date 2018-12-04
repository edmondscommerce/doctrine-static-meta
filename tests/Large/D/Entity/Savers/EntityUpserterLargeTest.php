<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: ec
 * Date: 04/12/18
 * Time: 16:58
 */

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\D\Entity\Savers;


use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

class EntityUpserterLargeTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/EntityFactoryTest';

    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE .
    TestCodeGenerator::TEST_ENTITY_EMAIL;

    private const TEST_VALUES = [
    ];
    protected static $buildOnce = true;
    private $entityFqn;
    /**
     * @var
     */
    private $upserter;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY_FQN);
        $this->upserter   = '';
        $this->factory->setEntityManager($this->getEntityManager());
    }
}