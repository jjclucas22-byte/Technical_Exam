<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(
        Request $request,
        CustomerSearchService $searchService
    ): JsonResponse {
        $search = trim((string) $request->query('search', ''));

        if ($search === '') {
            $customers = Customer::query()
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();

            return response()->json([
                'data' => $customers,
            ]);
        }

        $customerIds = $searchService->searchIds($search);

        $customersById = Customer::query()
            ->whereKey($customerIds)
            ->get()
            ->keyBy('id');

        $customers = collect($customerIds)
            ->map(
                static fn (int $id) => $customersById->get($id)
            )
            ->filter()
            ->values();

        return response()->json([
            'data' => $customers,
        ]);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::query()->create($request->validated());

        return response()->json([
            'message' => 'Customer created successfully.',
            'data' => $customer,
        ], 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'data' => $customer,
        ]);
    }

    public function update(
        UpdateCustomerRequest $request,
        Customer $customer
    ): JsonResponse {
        $customer->update($request->validated());

        return response()->json([
            'message' => 'Customer updated successfully.',
            'data' => $customer->fresh(),
        ]);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return response()->json(null, 204);
    }
}