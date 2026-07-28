<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $deliveryType = fake()->randomElement(['delivery', 'pickup']);

        return [
            'code'            => '',   // set via Order::booted() after create
            'customer_name'   => fake('pt_BR')->name(),
            'customer_phone'  => fake('pt_BR')->cellphoneNumber(),
            'customer_email'  => fake()->optional(0.6)->safeEmail(),
            'delivery_type'   => $deliveryType,
            'address'         => $deliveryType === 'delivery' ? fake('pt_BR')->streetAddress() : null,
            'notes'           => fake()->optional(0.3)->sentence(),
            'status'          => fake()->randomElement([
                Order::STATUS_PENDING,
                Order::STATUS_PREPARING,
                Order::STATUS_OUT_FOR_DELIVERY,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELED,
            ]),
            'total'           => 0.00, // recalculated after items are attached
            'created_at'      => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => Order::STATUS_PENDING]);
    }

    public function completed(): static
    {
        return $this->state(['status' => Order::STATUS_COMPLETED]);
    }
}
