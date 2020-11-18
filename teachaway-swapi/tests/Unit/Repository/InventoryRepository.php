<?php

declare(strict_types=1);

namespace Tests\Unit\Repository;

use App\Exceptions\InventoryException;
use App\Model\Inventory;
use GuzzleHttp\Client;
use Tests\TestCase;

class InventoryRepository extends TestCase
{
    /**
     * @test
     * @covers \App\Repository\InventoryRepository::fetch()
     */
    public function fetch_existing_inventory()
    {
        $inventory = factory(Inventory::class)->make();
        $inventoryRepository = new \App\Repository\InventoryRepository(new Client());

        // mock Eloquent model and static methods using facades
        $inventoryMock = \Facades\App\Model\Inventory::shouldReceive('where')
            ->with([
                'unit_type' => $inventory->unit_type,
                'tag' => $inventory->tag,
                'criteria' => $inventory->criteria,
            ])
            ->once()
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('first')
            ->withNoArgs()
            ->andReturn($inventory)
            ->getMock();

        $this->assertEquals(
            $inventory,
            $inventoryRepository->fetch($inventory->unit_type, $inventory->tag)
        );
    }

    /**
     * @test
     * @covers \App\Repository\InventoryRepository::fetch()
     */
    public function fetch_not_existing_inventory_then_request_swapi()
    {
        $inventory = factory(Inventory::class)->make();

        // mock Eloquent model and static methods using facades
        $inventoryMock = \Facades\App\Model\Inventory::shouldReceive('where')
            ->withAnyArgs()
            ->twice()
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('first')
            ->withNoArgs()
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('create')
            ->withAnyArgs()
            ->andReturn($inventory)
            ->getMock();

        $inventoryRepository = new \App\Repository\InventoryRepository($this->mockGuzzleWithResponse(Inventory::UNIT_TYPE_VEHICLE));

        $this->assertEquals(
            $inventory,
            $inventoryRepository->fetch($inventory->unit_type, $inventory->tag)
        );

        $inventory->setAttribute('payload', []);

        $inventoryRepository = new \App\Repository\InventoryRepository($this->mockGuzzleWithException());

        $this->assertEquals(
            $inventory,
            $inventoryRepository->fetch($inventory->unit_type, $inventory->tag)
        );
    }

    /**
     * @test
     * @covers \App\Repository\InventoryRepository::update()
     */
    public function update_existing_inventory()
    {
        $count = 10;
        $inventory = factory(Inventory::class)->make();
        $expected = clone $inventory;
        $expected->setAttribute('count', $count);

        // mock Eloquent model and static methods using facades
        $inventoryMock = \Facades\App\Model\Inventory::shouldReceive('findOrFail')
            ->withAnyArgs()
            ->once()
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('update')
            ->withAnyArgs()
            ->getMock()
            ->shouldReceive('toArray')
            ->andReturn($expected->toArray())
            ->getMock();

        /** @var \App\Repository\InventoryRepository $inventoryRepository */
        $inventoryRepository = app(\App\Repository\InventoryRepository::class);

        $this->assertEquals(
            $expected->toArray(),
            $inventoryRepository->update($inventory->id, $count)->toArray()
        );
    }

    /**
     * @test
     * @covers \App\Repository\InventoryRepository::update()
     */
    public function update_existing_inventory_fails_with_negative_count_and_throws_exception()
    {
        $id = 1;
        $count = -1;

        // mock Eloquent model and static methods using facades
        $inventoryMock = \Facades\App\Model\Inventory::shouldReceive('findOrFail')
            ->withAnyArgs()
            ->once()
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('update')
            ->withAnyArgs()
            ->getMock();

        /** @var \App\Repository\InventoryRepository $inventoryRepository */
        $inventoryRepository = app(\App\Repository\InventoryRepository::class);

        $this->throwException(new InventoryException("Cannot updates with negative count", 400));
        $inventoryRepository->update($id, $count);
    }

    /**
     * @test
     * @covers \App\Repository\InventoryRepository::increment()
     */
    public function increments_existing_inventory()
    {
        $inventory = factory(Inventory::class)->make();
        $expected = clone $inventory;
        $expected->setAttribute('count', $expected->count + 1);

        // mock Eloquent model and static methods using facades
        $inventoryMock = \Facades\App\Model\Inventory::shouldReceive('findOrFail')
            ->withAnyArgs()
            ->once()
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('update')
            ->withAnyArgs()
            ->getMock()
            ->shouldReceive('getAttribute')
            ->withAnyArgs()
            ->andReturn($inventory->count)
            ->getMock()
            ->shouldReceive('toArray')
            ->andReturn($expected->toArray())
            ->getMock();

        /** @var \App\Repository\InventoryRepository $inventoryRepository */
        $inventoryRepository = app(\App\Repository\InventoryRepository::class);

        $this->assertEquals(
            $expected->toArray(),
            $inventoryRepository->increment($inventory->id)->toArray()
        );
    }

    /**
     * @test
     * @covers \App\Repository\InventoryRepository::decrement()
     */
    public function decrements_existing_inventory()
    {
        $inventory = factory(Inventory::class)->make();
        $expected = clone $inventory;
        $expected->setAttribute('count', $expected->count - 1);

        // mock Eloquent model and static methods using facades
        $inventoryMock = \Facades\App\Model\Inventory::shouldReceive('findOrFail')
            ->withAnyArgs()
            ->once()
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('update')
            ->withAnyArgs()
            ->getMock()
            ->shouldReceive('getAttribute')
            ->withAnyArgs()
            ->andReturn($inventory->count)
            ->getMock()
            ->shouldReceive('toArray')
            ->andReturn($expected->toArray())
            ->getMock();

        /** @var \App\Repository\InventoryRepository $inventoryRepository */
        $inventoryRepository = app(\App\Repository\InventoryRepository::class);

        $this->assertEquals(
            $expected->toArray(),
            $inventoryRepository->increment($inventory->id)->toArray()
        );
    }

    /**
     * @test
     * @covers \App\Repository\InventoryRepository::decrement()
     */
    public function decrements_existing_inventory_fails_with_negative_count_and_throws_exception()
    {
        $id = 1;
        $count = -1;

        // mock Eloquent model and static methods using facades
        $inventoryMock = \Facades\App\Model\Inventory::shouldReceive('findOrFail')
            ->withAnyArgs()
            ->once()
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('update')
            ->withAnyArgs()
            ->getMock()
            ->shouldReceive('getAttribute')
            ->withAnyArgs()
            ->andReturn($count)
            ->getMock();

        /** @var \App\Repository\InventoryRepository $inventoryRepository */
        $inventoryRepository = app(\App\Repository\InventoryRepository::class);

        $this->throwException(new InventoryException("Cannot decrement to negative count", 400));
        $inventoryRepository->decrement($id);
    }
}
