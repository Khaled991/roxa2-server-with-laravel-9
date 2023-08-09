<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = QueryBuilder::for(Order::class)->allowedIncludes('products')->paginate();

        return new OrderCollection($orders);

    }

    public function show(Request $request, Order $order)
    {
        $order->load('products');

        if ($order->exists()) {
            return new OrderResource($order);
        }

        return response()->json(['message' => 'Order not found'], 404);

    }

    public function isExists($id)
    {
        $order = Order::find($id);

        $isOrderExists = $order !== null;

        return response()->json(['data' => ['is_exists' => $isOrderExists]]);
    }

    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        $order = Order::create($validated);

        $products = $this->mapProductsRequestToModels($request->input('products'));

        $order->products()->saveMany($products);

        return new OrderResource($order);
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $validated = $request->validated();

        $order->update($validated);

        $requestProducts = $request->input('products');
        if ($requestProducts) {
            $productModels = $this->mapProductsRequestToModels($requestProducts);

            $order->products()->delete();
            $order->products()->saveMany($productModels);
        }

        return (new OrderResource($order))->load('products');
    }

    private function mapProductsRequestToModels(array $products): array
    {
        $productData = [];

        foreach ($products as $product) {
            $createdProduct = new Product();
            $createdProduct->barcode = $product['barcode'];
            $createdProduct->quantity = $product['quantity'];
            $productData[] = $createdProduct;
        }

        return $productData;
    }

    public function destroy(Request $request, $id)
    {

        return response()->noContent();
    }
}
