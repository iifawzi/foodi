<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order(): void
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
    }
}
