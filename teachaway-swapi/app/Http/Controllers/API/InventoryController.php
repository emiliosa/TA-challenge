<?php

namespace App\Http\Controllers\API;

use App\Exceptions\InventoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Model\Inventory;
use App\Repository\InventoryRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * "unit_type": ["vehicle","starship"]
     * "tag": "Death Star"
     *
     * @TODO "tags": {
     *           "name": {
     *               "value": "death_stars"
     *           },
     *           "passengers_count": {
     *               "value": "3",
     *               "quantity": "gte"
     *           },
     *           "films_count": {
     *               "value": "3",
     *               "quantity": "gt"
     *           }
     *       }
     *
     * @param \App\Http\Requests\IndexInventoryRequest $request
     * @param \App\Repository\InventoryRepository $inventoryRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexInventoryRequest $request, InventoryRepository $inventoryRepository)
    {
        $unitType = $request->get('unit_type');
        $tag = $request->get('tags');

        return Response::json(
            $inventoryRepository
                ->fetch($unitType, $tag)
                ->toArray()
        );
    }

    /**
     * Update the specified Inventory count attribute.
     *
     * @param \App\Http\Requests\UpdateInventoryRequest $request
     * @param \App\Model\Inventory $inventory
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        $inventory->count = $request->input('count');
        $inventory->save();

        return Response::json($inventory);
    }

    /**
     * Increments the specified Inventory count attribute.
     *
     * @param \App\Model\Inventory $inventory
     * @return \Illuminate\Http\JsonResponse
     */
    public function increment(Inventory $inventory)
    {
        $inventory->count++;
        $inventory->save();

        return Response::json($inventory);
    }

    /**
     * Decrements the specified Inventory count attribute.
     *
     * @param \App\Model\Inventory $inventory
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\InventoryException
     */
    public function decrement(Inventory $inventory)
    {
        try {
            $inventory->count--;
            $inventory->save();
        } catch (QueryException $e) {
            Log::error(
                "Action: decrement" . PHP_EOL .
                "Inventory: id={$inventory->id} count={$inventory->count}" . PHP_EOL .
                "Message: {$e->getMessage()}" . PHP_EOL .
                "Code: {$e->getCode()}" . PHP_EOL .
                "Trace: {$e->getTraceAsString()}"
            );
            throw new InventoryException("Cannot decrement to negative count", 400);
        }

        return Response::json($inventory);
    }
}
