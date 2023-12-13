<?php

namespace Tests\Feature;

use App\Jobs\SendLowStockNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Src\Infrastructure\types\LowStockNotificationType;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;
    public function test_returns_422_when_validation_error(): void
    {
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

    public function test_returns_201_when_sufficient_ingredients_and_order_created(): void
    {
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
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertDatabaseHas('order_items', [
            'unit_price' => '100',
            'total_price' => '100',
            'quantity' => '1',
            'product_id' => 1,
        ]);

        // product 1 -> 150 g beef, 30 g cheese, 20 g onion
        // should be deducted from availability.
        $this->assertDatabaseHas('ingredient_stocks', [
            'available_quantity' => 850,
            'ingredient_id' => 1,
        ]);
        $this->assertDatabaseHas('ingredient_stocks', [
            'available_quantity' => 470,
            'ingredient_id' => 2,
        ]);
        $this->assertDatabaseHas('ingredient_stocks', [
            'available_quantity' => 1980,
            'ingredient_id' => 3,
        ]);
    }

    public function test_notifies_the_merchant_about_low_stock_only_once(): void
    {
        Queue::fake();
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

        // 6 * 150 ( 150 g for each burger = 900) -> we should have added notification notified the user.
        $this->assertDatabaseHas('low_stock_notifications', [
            'ingredient_id' => 1,
            'status' => LowStockNotificationType::PENDING,
        ]);
        Queue::assertPushed(SendLowStockNotification::class, function ($job) {
            $ids = $job->ids;
            return count($ids) == 1;
        });
    }

    public function test_returns_200_when_insufficient_ingredients(): void
    {
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
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);

    }
}
