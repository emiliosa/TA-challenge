<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Repository\InventoryRepository;
use Illuminate\Support\Facades\Response;

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
     * @param int $id
     * @param \App\Http\Requests\UpdateInventoryRequest $request
     * @param \App\Repository\InventoryRepository $inventoryRepository
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\InventoryException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, UpdateInventoryRequest $request, InventoryRepository $inventoryRepository)
    {
        return Response::json($inventoryRepository->update($id, $request->input('count')));
    }

    /**
     * Increments the specified Inventory count attribute.
     *
     * @param int $id
     * @param \App\Repository\InventoryRepository $inventoryRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function increment(int $id, InventoryRepository $inventoryRepository)
    {
        return Response::json($inventoryRepository->increment($id));
    }

    /**
     * Decrements the specified Inventory count attribute.
     *
     * @param int $id
     * @param \App\Repository\InventoryRepository $inventoryRepository
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\InventoryException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function decrement(int $id, InventoryRepository $inventoryRepository)
    {
        return Response::json($inventoryRepository->decrement($id));
    }
}
