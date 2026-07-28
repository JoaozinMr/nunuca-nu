<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    /**
     * Persist a new order from the client-side cart and redirect to confirmation.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        try {
            $order = $this->orderService->createFromCart($request->validated());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()
            ->route('order.confirmation', $order->code)
            ->with('success', "Pedido {$order->code} recebido com sucesso!");
    }

    /**
     * Order confirmation page — shows the PIX payment instructions and order summary.
     */
    public function confirmation(string $code): Response|RedirectResponse
    {
        $order = Order::with('items')
            ->where('code', $code)
            ->first();

        if (! $order) {
            return redirect()->route('home')->with('error', 'Pedido não encontrado.');
        }

        return Inertia::render('OrderConfirmation', [
            'order' => new OrderResource($order),
            'pix'   => [
                'key'         => env('PIX_KEY', ''),
                'beneficiary' => env('PIX_BENEFICIARY', 'nunuca.nu'),
            ],
        ]);
    }
}
