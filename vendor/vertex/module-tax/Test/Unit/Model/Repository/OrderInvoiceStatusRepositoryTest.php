<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model\Repository;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vertex\Tax\Model\Data\OrderInvoiceStatus as Model;
use Vertex\Tax\Model\Data\OrderInvoiceStatusFactory as Factory;
use Vertex\Tax\Model\Repository\OrderInvoiceStatusRepository as Repository;
use Vertex\Tax\Model\ResourceModel\OrderInvoiceStatus as ResourceModel;
use Vertex\Tax\Test\Unit\TestCase;

/**
 * Tests for {@see Repository}
 */
class OrderInvoiceStatusRepositoryTest extends TestCase
{
    /** @var Repository */
    private $repository;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ResourceModel */
    private $resourceModelMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Factory */
    private $factoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Model */
    private $modelMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->resourceModelMock = $this->getMockBuilder(ResourceModel::class)
            ->setMethods(['save', 'delete', 'load'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->modelMock = $this->getMockBuilder(Model::class)
            ->setMethods(['setId', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->factoryMock = $this->getMockBuilder(Factory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getObject(
            Repository::class,
            [
                'resourceModel' => $this->resourceModelMock,
                'factory' => $this->factoryMock,
            ]
        );
    }

    /**
     * Test Repository saves resource model
     *
     * @return void
     */
    public function testSaveHappyPath()
    {
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->with($this->modelMock);

        $this->repository->save($this->modelMock);
    }

    /**
     * Test AlreadyExistsExceptions are passed through during an attempted save
     *
     * @return void
     */
    public function testSaveAlreadyExists()
    {
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->with($this->modelMock)
            ->willThrowException(new AlreadyExistsException(__('test')));

        $this->expectException(AlreadyExistsException::class);
        $this->expectExceptionMessage('test');

        $this->repository->save($this->modelMock);
    }

    /**
     * Test unexpected Exceptions are converted to CouldNotSaveExceptions
     *
     * @return void
     */
    public function testSaveOtherException()
    {
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->with($this->modelMock)
            ->willThrowException(new \Exception('test'));

        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('Unable to save Order Invoice Sent status');

        $this->repository->save($this->modelMock);
    }

    /**
     * Test Repository calls delete on Resource Model
     *
     * @return void
     */
    public function testDeleteHappyPath()
    {
        $this->resourceModelMock->expects($this->once())
            ->method('delete')
            ->with($this->modelMock);

        $this->repository->delete($this->modelMock);
    }

    /**
     * Test unexpected exceptions are converted to CouldNotDeleteException
     *
     * @return void
     */
    public function testDeleteException()
    {
        $this->resourceModelMock->expects($this->once())
            ->method('delete')
            ->with($this->modelMock)
            ->willThrowException(new \Exception('test'));

        $this->expectException(CouldNotDeleteException::class);
        $this->expectExceptionMessage('Unable to delete Order Invoice Sent status');

        $this->repository->delete($this->modelMock);
    }

    /**
     * Test delete by id creates model with proper ID and passes it to resource model
     *
     * @return void
     */
    public function testDeleteOrderByIdHappyPath()
    {
        $orderId = rand();

        $this->factoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->modelMock);

        $this->modelMock->expects($this->once())
            ->method('setId')
            ->with($orderId);

        $this->resourceModelMock->expects($this->once())
            ->method('delete')
            ->with($this->modelMock);

        $this->repository->deleteByOrderId($orderId);
    }

    /**
     * Test unexpected Exceptions are converted to CouldNotDeleteException
     *
     * @return void
     */
    public function testDeleteOrderByIdException()
    {
        $orderId = rand();

        $this->factoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->modelMock);

        $this->modelMock->expects($this->once())
            ->method('setId')
            ->with($orderId);

        $this->resourceModelMock->expects($this->once())
            ->method('delete')
            ->with($this->modelMock)
            ->willThrowException(new \Exception('test'));

        $this->expectException(CouldNotDeleteException::class);
        $this->expectExceptionMessage('Unable to delete Order Invoice Sent status');

        $this->repository->deleteByOrderId($orderId);
    }

    /**
     * Test get by order ID properly passes through order id
     *
     * @return void
     */
    public function testGetByOrderIdHappyPath()
    {
        $orderId = rand();

        $this->factoryMock
            ->method('create')
            ->willReturn($this->modelMock);

        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($this->modelMock, $orderId);

        $this->modelMock->method('getId')
            ->willReturn($orderId);

        $this->repository->getByOrderId($orderId);
    }

    /**
     * Test that when no order is loaded from the DB, NoSuchEntityException is thrown
     *
     * @return void
     */
    public function testGetByOrderIdException()
    {
        $orderId = rand();

        $this->factoryMock->method('create')
            ->willReturn($this->modelMock);

        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($this->modelMock, $orderId);

        $this->modelMock->method('getId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);

        $this->repository->getByOrderId($orderId);
    }
}
