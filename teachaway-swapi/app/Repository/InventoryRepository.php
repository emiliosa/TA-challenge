<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Inventory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * Class InventoryRepository
 * @package App\Repository
 */
class InventoryRepository
{
    protected Client $client;

    /**
     * InventoryRepository constructor.
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return Inventory::class;
    }

    /**
     * Fetch inventory by $unitType and $tag.
     * Make request to swapi.
     *
     * @param string $unitType
     * @param string $tag
     * @return \App\Model\Inventory
     */
    public function fetch(string $unitType, string $tag): Inventory
    {
        $inventory = Inventory::where([
            'unit_type' => $unitType,
            'tag' => $tag,
            'criteria' => Inventory::CRITERIA_PM
        ])->first();

        if (!$inventory) {
            $inventory = $this->makeRequest($unitType, $tag);
        }

        return $inventory;
    }

    /**
     * Make request to swapi and consume payload.
     * If exception occurs, inventory's count and payload are empty.
     *
     * @param string $unitType
     * @param string $tag
     * @return \App\Model\Inventory
     */
    protected function makeRequest(string $unitType, string $tag): Inventory
    {
        $uri = "/api/{$unitType}s?search={$tag}";
        $inventory = new Inventory();
        $inventory->unit_type = $unitType;
        $inventory->criteria = Inventory::CRITERIA_PM;
        $inventory->tag = $tag;

        try {
            $payload = [];
            Log::info("[GET] {$uri}");
            $response = $this->client->request('GET', $uri);
            $response = json_decode($response->getBody()->getContents(), true);
            $count = $response['count'];
            $payload[] = $response;

            for ($page=1 ; $response['next'] !== null; ++$page) {
                $uri = "/api/{$unitType}s?search={$tag}&page={$page}";
                $response = $this->client->request('GET', $uri);
                $response = json_decode($response->getBody()->getContents(), true);
                $payload[] = $response;
                $count += $response['count'];
            }

            $inventory->count = $count;
            $inventory->payload = json_encode($payload);
        } catch (GuzzleException $e) {
            Log::error(
                "URI: {$uri}" . PHP_EOL .
                "Message: {$e->getMessage()}" . PHP_EOL .
                "Code: {$e->getCode()}" . PHP_EOL .
                "Trace: {$e->getTraceAsString()}"
            );
            $inventory->count = 0;
            $inventory->payload = [];
        }

        $inventory->save();

        return $inventory;
    }
}
