<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Repository;

use Vertex\Tax\Model\Data\CustomerCodeFactory;
use Vertex\Tax\Model\Registry\CustomerCodeRegistry;
use Vertex\Tax\Model\Repository\CustomerCodeRepository;
use Vertex\Tax\Model\ResourceModel\CustomerCode;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * Tests for the CustomerCodeRepository
 */
class CustomerCodeRepositoryTest extends TestCase
{
    /** @var CustomerCodeRepository */
    private $repository;

    /** @var CustomerCodeRegistry */
    private $registry;

    /** @var CustomerCode */
    private $resourceModel;

    /** @var CustomerCodeFactory */
    private $factory;

    /**
     * Setup the repository and it's dependencies for testing
     */
    protected function setUp()
    {
        parent::setUp();
        $this->registry = $this->getObject(CustomerCodeRegistry::class);
        $this->resourceModel = $this->createMock(CustomerCode::class);
        $this->factory = $this->getMockBuilder(\Vertex\Tax\Model\Data\CustomerCodeFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->factory->method('create')
            ->willReturnCallback(
                function () {
                    return $this->getObject(\Vertex\Tax\Model\Data\CustomerCode::class);
                }
            );
        $this->repository = $this->getObject(
            CustomerCodeRepository::class,
            [
                'registry' => $this->registry,
                'resourceModel' => $this->resourceModel,
                'factory' => $this->factory,
            ]
        );
    }

    /**
     * Ensure we attempt to load a customer code from the database if it is not in the registry
     */
    public function testLoadsIfNotInRegistry()
    {
        $customerId = rand(0, 100);
        $customerCodeVal = uniqid('customer-code');

        $this->resourceModel->expects($this->once())
            ->method('load')
            ->willReturnCallback(
                function ($customerCode, $customerId) use ($customerCodeVal) {
                    $customerCode->setId(1);
                    $customerCode->setCustomerCode($customerCodeVal);
                    $customerCode->setCustomerId($customerId);
                }
            );

        $customerCode = $this->repository->getByCustomerId($customerId);

        $this->assertEquals($customerId, $customerCode->getCustomerId());
        $this->assertEquals($customerCodeVal, $customerCode->getCustomerCode());
    }

    /**
     * Ensure we do not attempt to load a customer code from the database if it is in the registry
     */
    public function testDoesNotLoadIfInRegistry()
    {
        $customerId = rand(0, 100);
        $customerCodeVal = uniqid('customer-code');

        $this->registry->set($customerId, $customerCodeVal);

        $this->resourceModel->expects($this->never())
            ->method('load');

        $customerCode = $this->repository->getByCustomerId($customerId);

        $this->assertEquals($customerId, $customerCode->getCustomerId());
        $this->assertEquals($customerCodeVal, $customerCode->getCustomerCode());
    }

    /**
     * Ensure that a Customer Code is properly saved when it's not already set to the value to save in the registry
     */
    public function testSavesIfNotInRegistry()
    {
        $customerId = rand(0, 100);
        $customerCodeVal = uniqid('customer-code');

        $code = $this->factory->create();
        $code->setCustomerId($customerId);
        $code->setCustomerCode($customerCodeVal);

        $this->resourceModel->expects($this->once())
            ->method('save')
            ->with($code);

        $this->repository->save($code);

        $this->assertEquals($customerCodeVal, $this->registry->get($customerId));
    }

    /**
     * Ensure that no DB call is made to save the customer code if the value to save is whats in the registry
     */
    public function testDoesNotSaveIfInRegistry()
    {
        $customerId = rand(0, 100);
        $customerCodeVal = uniqid('customer-code');

        $code = $this->factory->create();
        $code->setCustomerId($customerId);
        $code->setCustomerCode($customerCodeVal);

        $this->registry->set($customerId, $customerCodeVal);

        $this->resourceModel->expects($this->never())
            ->method('save');

        $this->repository->save($code);
    }
}
