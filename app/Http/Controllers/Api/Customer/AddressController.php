<?php

namespace App\Http\Controllers\Api\Customer;

use App\Domain\User\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiResponse;

    public function __construct(
        private UserService $userService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $addresses = $this->userService->getAddresses($request->user()->id);

        return $this->success(
            AddressResource::collection($addresses),
            'Addresses retrieved successfully',
        );
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $address = $this->userService->createAddress(
            $request->user()->id,
            $request->validated(),
        );

        return $this->created(
            new AddressResource($address),
            'Address created successfully',
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $address = $this->userService->findAddress($id);

        if (!$address || $address->user_id !== $request->user()->id) {
            return $this->notFound('Address not found');
        }

        return $this->success(
            new AddressResource($address),
            'Address retrieved successfully',
        );
    }

    public function update(UpdateAddressRequest $request, int $id): JsonResponse
    {
        $address = $this->userService->findAddress($id);

        if (!$address || $address->user_id !== $request->user()->id) {
            return $this->notFound('Address not found');
        }

        $updated = $this->userService->updateAddress($id, $request->validated());

        return $this->success(
            new AddressResource($updated),
            'Address updated successfully',
        );
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $address = $this->userService->findAddress($id);

        if (!$address || $address->user_id !== $request->user()->id) {
            return $this->notFound('Address not found');
        }

        $this->userService->deleteAddress($id);

        return $this->success(null, 'Address deleted successfully');
    }
}
