<?php

namespace Tests;

use App\Model\Inventory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function mockGuzzleWithResponse(string $unitType = null): Client
    {
        $fileName = null;

        switch ($unitType) {
            case Inventory::UNIT_TYPE_STARSHIP:
                $fileName = 'starships.json';
                break;
            case Inventory::UNIT_TYPE_VEHICLE:
                $fileName = 'vehicles.json';
                break;
            default:
                $fileName = 'empty.json';
                break;
        }
        $path = storage_path("testing/data/SWAPI/{$fileName}");
        $json = file_get_contents($path);

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $json),
        ]);
        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }

    protected function mockGuzzleWithException(): Client
    {
        $mock = new MockHandler([
            new RequestException('Error Communicating with SWAPI', new Request('GET', 'test')),
        ]);
        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }
}
