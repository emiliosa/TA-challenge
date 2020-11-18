<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\API;

use App\Exceptions\InventoryException;
use App\Model\Inventory;
use App\Repository\InventoryRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class InventoryController extends TestCase
{
    /**
     * @test
     * @covers \App\Http\Controllers\API\InventoryController::index()
     */
    public function get_vehicle_inventory_resource()
    {
        $params = ['unit_type' => Inventory::UNIT_TYPE_VEHICLE];
        $states = [
            Inventory::UNIT_TYPE_VEHICLE,
            Inventory::UNIT_TYPE_VEHICLE_NO_TAGS,
            Inventory::UNIT_TYPE_VEHICLE_EMPTY,
        ];

        foreach ($states as $state) {
            $inventory = factory(Inventory::class)->state($state)->make();
            $this->mock(InventoryRepository::class, function ($mock) use ($inventory) {
                $mock->shouldReceive('fetch')
                    ->withAnyArgs()
                    ->once()
                    ->andReturn($inventory);
            });

            $response = $this->json('GET', '/api/inventory', $params);
            $response->assertStatus(200);
            $response->assertExactJson($inventory->toArray());
        }
    }

    /**
     * @test
     * @covers \App\Http\Controllers\API\InventoryController::index()
     */
    public function get_starship_inventory_resource()
    {
        $params = ['unit_type' => Inventory::UNIT_TYPE_STARSHIP];
        $states = [
            Inventory::UNIT_TYPE_STARSHIP,
            Inventory::UNIT_TYPE_STARSHIP_NO_TAGS,
            Inventory::UNIT_TYPE_STARSHIP_EMPTY,
        ];

        foreach ($states as $state) {
            $inventory = factory(Inventory::class)->state($state)->make();
            $this->mock(InventoryRepository::class, function ($mock) use ($inventory) {
                $mock->shouldReceive('fetch')
                    ->withAnyArgs()
                    ->once()
                    ->andReturn($inventory);
            });

            $response = $this->json('GET', '/api/inventory', $params);
            $response->assertStatus(200);
            $response->assertExactJson($inventory->toArray());
        }
    }

    /**
     * @test
     * @covers \App\Http\Controllers\API\InventoryController::index()
     */
    public function get_not_valid_inventory_resource()
    {
        $params = ['unit_type' => 'test'];
        $expected = [
            "message" => "The given data was invalid.",
            "errors" => [
                "unit_type" => [
                    "Bad unit type, expected [starship,vehicle]",
                ],
            ],
        ];

        $response = $this->json('GET', '/api/inventory', $params);
        $response->assertStatus(422);
        $response->assertExactJson($expected);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\API\InventoryController::update()
     */
    public function update_vehicle_inventory_resource_count()
    {
        $body = ['count' => 10];
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        $inventory = factory(Inventory::class)->state(Inventory::UNIT_TYPE_VEHICLE)->make();
        $expected = $inventory;
        $expected->setAttribute('count', 10);

        $this->mock(InventoryRepository::class, function ($mock) use ($inventory) {
            $mock->shouldReceive('update')
                ->withAnyArgs()
                ->once()
                ->andReturn($inventory);
        });

        $response = $this->patch("/api/inventory/{$inventory->id}", $body, $headers);
        $response->assertStatus(200);
        $response->assertExactJson($expected->toArray());
    }

    /**
     * @test
     * @covers \App\Http\Controllers\API\InventoryController::update()
     */
    public function update_vehicle_inventory_resource_not_found()
    {
        $body = ['count' => 10];
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        $inventory = factory(Inventory::class)->state(Inventory::UNIT_TYPE_VEHICLE)->make();
        $expected = [
            "message" => "No query results for model [App\\Model\\Inventory] {$inventory->id}",
        ];

        $this->mock(InventoryRepository::class, function ($mock) use ($inventory, $expected) {
            $mock->shouldReceive('update')
                ->withAnyArgs()
                ->once()
                ->andThrows(ModelNotFoundException::class, $expected['message']);
        });

        $response = $this->patch("/api/inventory/{$inventory->id}", $body, $headers);
        $response->assertStatus(404);
        $response->assertExactJson($expected);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\API\InventoryController::update()
     */
    public function update_vehicle_inventory_resource_fails_with_negative_count()
    {
        $body = ['count' => -1];
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        $inventory = factory(Inventory::class)->state(Inventory::UNIT_TYPE_VEHICLE)->make();
        $expected = [
            "message" => "The given data was invalid.",
            "errors" => [
                "count" => [
                    "The count must be at least 0.",
                ],
            ],
        ];

        $response = $this->patch("/api/inventory/{$inventory->id}", $body, $headers);
        $response->assertStatus(422);
        $response->assertExactJson($expected);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\API\InventoryController::increment()
     */
    public function increments_vehicle_inventory_resource_count()
    {
        $expected = factory(Inventory::class)->state(Inventory::UNIT_TYPE_VEHICLE)->make();
        $expected->setAttribute('count', $expected->count + 1);

        $this->mock(InventoryRepository::class, function ($mock) use ($expected) {
            $mock->shouldReceive('increment')
                ->withAnyArgs()
                ->once()
                ->andReturn($expected);
        });

        $response = $this->post("/api/inventory/{$expected->id}/increment");
        $response->assertStatus(200);
        $response->assertExactJson($expected->toArray());
    }

    /**
     * @test
     * @covers \App\Http\Controllers\API\InventoryController::increment()
     */
    public function increments_vehicle_inventory_resource_not_found()
    {
        $inventory = factory(Inventory::class)->state(Inventory::UNIT_TYPE_VEHICLE)->make();
        $expected = [
            "message" => "No query results for model [App\\Model\\Inventory] {$inventory->id}",
        ];

        $this->mock(InventoryRepository::class, function ($mock) use ($inventory, $expected) {
            $mock->shouldReceive('increment')
                ->withAnyArgs()
                ->once()
                ->andThrows(ModelNotFoundException::class, $expected['message']);
        });

        $response = $this->post("/api/inventory/{$inventory->id}/increment");
        $response->assertStatus(404);
        $response->assertExactJson($expected);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\API\InventoryController::decrement()
     */
    public function decrements_vehicle_inventory_resource_count()
    {
        $expected = factory(Inventory::class)->state(Inventory::UNIT_TYPE_VEHICLE)->make();
        $expected->setAttribute('count', $expected->count - 1);

        $this->mock(InventoryRepository::class, function ($mock) use ($expected) {
            $mock->shouldReceive('decrement')
                ->withAnyArgs()
                ->once()
                ->andReturn($expected);
        });

        $response = $this->post("/api/inventory/{$expected->id}/decrement");
        $response->assertStatus(200);
        $response->assertExactJson($expected->toArray());
    }

    /**
     * @test
     * @covers \App\Http\Controllers\API\InventoryController::decrement()
     */
    public function decrements_vehicle_inventory_resource_not_found()
    {
        $inventory = factory(Inventory::class)->state(Inventory::UNIT_TYPE_VEHICLE)->make();
        $expected = [
            "message" => "No query results for model [App\\Model\\Inventory] {$inventory->id}",
        ];

        $this->mock(InventoryRepository::class, function ($mock) use ($inventory, $expected) {
            $mock->shouldReceive('decrement')
                ->withAnyArgs()
                ->once()
                ->andThrows(ModelNotFoundException::class, $expected['message']);
        });

        $response = $this->post("/api/inventory/{$inventory->id}/decrement");
        $response->assertStatus(404);
        $response->assertExactJson($expected);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\API\InventoryController::decrement()
     */
    public function decrements_vehicle_inventory_resource_fails_to_negative_count()
    {
        $inventory = factory(Inventory::class)->state(Inventory::UNIT_TYPE_VEHICLE)->make();
        $inventory->setAttribute('count', 0);
        $expected = [
            "message" => "Cannot decrement to negative count",
        ];

        $this->mock(InventoryRepository::class, function ($mock) use ($expected) {
            $mock->shouldReceive('decrement')
                ->withAnyArgs()
                ->once()
                ->andThrows(InventoryException::class, $expected['message']);
        });

        $response = $this->post("/api/inventory/{$inventory->id}/decrement");
        $response->assertStatus(400);
        $response->assertExactJson($expected);
    }
}
