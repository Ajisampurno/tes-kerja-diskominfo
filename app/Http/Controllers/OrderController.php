<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderDetails.product')->get();

        $orderList = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'products' => $order->orderDetails->map(function ($detail) {
                    return [
                        'id' => $detail->product->id,
                        'name' => $detail->product->name,
                        'price' => $detail->product->price,
                        'quantity' => $detail->quantity,
                        'stock' => $detail->product->stock,
                        'sold' => $detail->product->sold,
                        'created_at' => $detail->product->created_at,
                        'updated_at' => $detail->product->updated_at,
                    ];
                }),
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Order List',
            'data' => $orderList,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = new Order();
        $order->save();

        $orderProducts = [];

        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            
            if ($product && $product->stock >= $productData['quantity']) {
                $product->stock -= $productData['quantity'];
                $product->sold += $productData['quantity'];
                $product->save();

                $orderDetail = OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                ]);

                $orderProducts[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $orderDetail->quantity,
                    'stock' => $product->stock,
                    'sold' => $product->sold,
                    'created_at' => $product->created_at,
                    'updated_at' => now(),
                ];
            }
        }

        return response()->json([
            'message' => 'Order created',
            'data' => [
                'id' => $order->id,
                'products' => $orderProducts,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], 201);
    }

    public function show($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        $orderDetails = $order->orderDetails()->with('product')->get();

        $products = $orderDetails->map(function ($orderDetail) {
            return [
                'id' => $orderDetail->product->id,
                'name' => $orderDetail->product->name,
                'price' => $orderDetail->product->price,
                'quantity' => $orderDetail->quantity,
                'stock' => $orderDetail->product->stock,
                'sold' => $orderDetail->product->sold,
                'created_at' => $orderDetail->product->created_at,
                'updated_at' => $orderDetail->product->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Order Detail',
            'data' => [
                'id' => $order->id,
                'products' => $products,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ],
        ], 200);
    }

    public function destroy($id)
    {
        $order = Order::with('orderDetails.product')->find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        $orderDetails = $order->orderDetails;

        foreach ($orderDetails as $orderDetail) {
            $product = $orderDetail->product;
            $product->stock += $orderDetail->quantity;
            $product->sold -= $orderDetail->quantity;
            $product->save();
        }

        $order->orderDetails()->delete();
        $order->delete();

        $products = $orderDetails->map(function ($orderDetail) {
            return [
                'id' => $orderDetail->product->id,
                'name' => $orderDetail->product->name,
                'price' => $orderDetail->product->price,
                'quantity' => $orderDetail->quantity,
                'stock' => $orderDetail->product->stock,
                'sold' => $orderDetail->product->sold,
                'created_at' => $orderDetail->product->created_at,
                'updated_at' => $orderDetail->product->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Order deleted successfully',
            'data' => [
                'id' => $order->id,
                'products' => $products,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ],
        ], 200);
    }

}
