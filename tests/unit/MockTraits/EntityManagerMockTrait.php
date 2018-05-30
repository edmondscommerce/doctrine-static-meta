<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\MockTraits;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

trait EntityManagerMockTrait
{
    private function getEntityManagerMock()
    {
        /**
         * @var $this TestCase
         */
        return $this->getMockBuilder(EntityManagerInterface::class)
                    ->setMethods(['getRepository', 'getClassMetadata', 'persist', 'flush', 'find'])
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
