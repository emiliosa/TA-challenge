<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exceptions\InventoryException;
use App\Model\Inventory;
use \Facades\App\Model\Inventory as InventoryFacade;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\QueryException;
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
    public static function getModel(): string
    {
        return Inventory::class;
    }

    /**
     * Fetch inventory by $unitType and $tag.
     * Make request to swapi.
     *
     * @param string $unitType
     * @param string|null $tag
     * @return \App\Model\Inventory
     */
    public function fetch(string $unitType, string $tag = null): Inventory
    {
        $args = [
            'unit_type' => $unitType,
            'tag' => $tag,
            'criteria' => $tag ? Inventory::CRITERIA_PM : null,
        ];

        $inventory = InventoryFacade::where($args)->first();

        if (!$inventory) {
            $inventory = $this->makeRequest($unitType, $tag);
        }

        return $inventory;
    }

    /**
     * Updates Inventory count property.
     *
     * @param int $id
     * @param int $count
     * @return \App\Model\Inventory
     * @throws \App\Exceptions\InventoryException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, int $count): Inventory
    {
        $inventory = InventoryFacade::findOrFail($id);

        try {
            $inventory->update(['count' => $count]);
        } catch (QueryException $e) {
            Log::error(
                "Action: decrement".PHP_EOL.
                "Inventory: id={$inventory->id} count={$inventory->count}".PHP_EOL.
                "Message: {$e->getMessage()}".PHP_EOL.
                "Code: {$e->getCode()}".PHP_EOL.
                "Trace: {$e->getTraceAsString()}"
            );
            throw new InventoryException("Cannot updates with negative count", 400);
        }

        return $inventory;
    }

    /**
     * Increments Inventory count property.
     *
     * @param int $id
     * @return \App\Model\Inventory
     */
    public function increment(int $id): Inventory
    {
        $inventory = InventoryFacade::findOrFail($id);
        $inventory->update(['count' => $inventory->count + 1]);

        return $inventory;
    }

    /**
     * Decrements Inventory count property.
     *
     * @param int $id
     * @return \App\Model\Inventory
     * @throws \App\Exceptions\InventoryException
     */
    public function decrement(int $id): Inventory
    {
        $inventory = InventoryFacade::findOrFail($id);

        try {
            $inventory->update(['count' => $inventory->count - 1]);
        } catch (QueryException $e) {
            Log::error(
                "Action: decrement".PHP_EOL.
                "Inventory: id={$inventory->id} count={$inventory->count}".PHP_EOL.
                "Message: {$e->getMessage()}".PHP_EOL.
                "Code: {$e->getCode()}".PHP_EOL.
                "Trace: {$e->getTraceAsString()}"
            );
            throw new InventoryException("Cannot decrement to negative count", 400);
        }

        return $inventory;
    }

    /**
     * Make request to swapi and consume payload.
     * If exception occurs, inventory's count and payload are empty.
     *
     * @param string $unitType
     * @param string|null $tag
     * @return \App\Model\Inventory
     */
    protected function makeRequest(string $unitType, string $tag = null): Inventory
    {
        $attributes['unit_type'] = $unitType;
        $attributes['criteria'] = $tag ? Inventory::CRITERIA_PM : null;
        $attributes['tag'] = $tag;

        try {
            $count = 0;
            $payload = [];
            $hasNext = true;

            for ($page = 1; $hasNext; $page++) {
                if ($tag) {
                    $uri = "/api/{$unitType}s?search={$tag}&page={$page}";
                } else {
                    $uri = "/api/{$unitType}s?page={$page}";
                }
                Log::info("[GET] {$uri}");
                $response = $this->client->request('GET', $uri);
                $responseContent = json_decode($response->getBody()->getContents(), true);
                $payload[] = $responseContent;
                $count += $responseContent['count'];
                $hasNext = $responseContent['next'] !== null;
            }

            $attributes['count'] = $count;
            $attributes['payload'] = json_encode($payload);
        } catch (GuzzleException $e) {
            Log::error(
                "URI: {$uri}".PHP_EOL.
                "Message: {$e->getMessage()}".PHP_EOL.
                "Code: {$e->getCode()}".PHP_EOL.
                "Trace: {$e->getTraceAsString()}"
            );
            $attributes['count'] = 0;
            $attributes['payload'] = json_encode([]);
        }

        return InventoryFacade::create($attributes);
    }
}
