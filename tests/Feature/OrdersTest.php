<?php

namespace Tests\Feature;

use App\Jobs\SendLowStockNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_422_when_validation_error(): void
    {
        $this->seed();
        $data = [
            "merchantId" => 1,
            "products" => [
                "product_id" => 1,
                "quantity" => 1
            ]
        ];
        $response = $this->json('POST', '/api/v1/orders', $data);
        $response->assertStatus(422);
    }

    public function test_returns_422_when_merchant_id_invalid(): void
    {
        $this->seed();
        $data = [
            "merchantId" => -10,
            "products" => [
                [
                    "product_id" => 1,
                    "quantity" => 1
                ]
            ]
        ];
        $response = $this->json('POST', '/api/v1/orders', $data);
        $response->assertStatus(422);
    }

    public function test_returns_422_when_product_is_invalid(): void
    {
        $this->seed();
        $data = [
            "merchantId" => 1,
            "products" => [
                [
                    "product_id" => -10,
                    "quantity" => 1
                ]
            ]
        ];
        $response = $this->json('POST', '/api/v1/orders', $data);
        $response->assertStatus(422);
    }

    public function test_returns_422_when_quantity_is_invalid(): void
    {
        $this->seed();
        $data = [
            "merchantId" => 1,
            "products" => [
                [
                    "product_id" => 1,
                    "quantity" => "million"
                ]
            ]
        ];
        $response = $this->json('POST', '/api/v1/orders', $data);
        $response->assertStatus(422);
    }

    public function test_returns_422_when_merchant_doesnt_exist(): void
    {
        $this->seed();
        $data = [
            "merchantId" => 1000,
            "products" => [
                [
                    "product_id" => 1,
                    "quantity" => 1
                ]
            ]
        ];
        $response = $this->json('POST', '/api/v1/orders', $data);
        $response->assertStatus(422);
    }

    public function test_returns_422_when_product_doesnt_exist(): void
    {
        $this->seed();
        $data = [
            "merchantId" => 1,
            "products" => [
                [
                    "product_id" => 110,
                    "quantity" => 1
                ]
            ]
        ];
        $response = $this->json('POST', '/api/v1/orders', $data);
        $response->assertStatus(422);
    }

    public function test_returns_200_when_sufficient_ingredients_successfully(): void
    {
        $this->seed();
        $data = [
            "merchantId" => 1,
            "products" => [
                [
                    "product_id" => 1,
                    "quantity" => 1
                ]
            ]
        ];
        $response = $this->json('POST', '/api/v1/orders', $data);
        $response->assertStatus(200);
        $response->assertJsonFragment(["Your order has been confirmed successfully"]);
    }

    public function test_returns_201_when_sufficient_ingredients_and_order_created(): void
    {
        $this->seed();
        $data = [
            "merchantId" => 1,
            "products" => [
                [
                    "product_id" => 1,
                    "quantity" => 1
                ]
            ]
        ];
        $response = $this->json('POST', '/api/v1/orders', $data);
        $response->assertStatus(201);
        $response->assertJsonFragment(["Your order has been confirmed successfully"]);
    }

    public function test_notifies_the_merchant_about_low_stock_only_once(): void
    {
        Queue::fake();
        $this->seed();
        $data = [
            "merchantId" => 1,
            "products" => [
                [
                    "product_id" => 1,
                    "quantity" => 6
                ]
            ]
        ];
        $response = $this->json('POST', '/api/v1/orders', $data);
        $response->assertStatus(201);
        $response->assertJsonFragment(["Your order has been confirmed successfully"]);
        Queue::assertPushed(SendLowStockNotification::class, function ($job) {
           $ids = $job->ids;
            return count($ids) == 1;
        });
    }

    public function test_returns_200_when_insufficient_ingredients(): void
    {
        $this->seed();
        $data = [
            "merchantId" => 1,
            "products" => [
                [
                    "product_id" => 1,
                    "quantity" => 100
                ]
            ]
        ];
        $response = $this->json('POST', '/api/v1/orders', $data);
        $response->assertStatus(200);
        $response->assertJsonFragment(["Sorry, we have a shortage in the ingredients."]);
    }
}
