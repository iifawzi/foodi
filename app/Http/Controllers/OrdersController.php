<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreateOrderRequest;
use App\Http\shared\Respond;
use Illuminate\Http\JsonResponse;
use Src\Application\services\OrderService;
use Src\Domain\Types\OrderStatus;

class OrdersController extends Controller
{

    public function __construct(private readonly OrderService $orderService)
    {
    }

    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        try {
            ["status" => $status, "order" => $order] = $this->orderService->CreateOrder($request->toArray());

            if (!$status && !isset($order)) {
                return Respond::Error(423, 'Sorry, we have some issues in the kitchen');
            }

            if ($order->getStatus() == OrderStatus::CONFIRMED) {
                return Respond::Success(200, 'Your order has been confirmed successfully');
            }

            return Respond::Success(200, 'Sorry, we have a shortage in the ingredients.');

        } catch (\Exception $e) {
            return Respond::Error(500, $e->getMessage());
        }
    }
}
