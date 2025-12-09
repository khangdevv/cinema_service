<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\Seat;
use Illuminate\Http\Request;

class OrderLineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orderLine = OrderLine::query()->with(['order', 'product', 'seat']);

        if ($request->has('order_id')) {
            $orderLine->where('order_id', $request->order_id);
        }

        if ($request->has('item_type')) {
            $orderLine->where('item_type', $request->item_type);
        }

        if ($request->has('product_id')) {
            $orderLine->where('product_id', $request->product_id);
        }

        if ($request->has('seat_id')) {
            $orderLine->where('seat_id', $request->seat_id);
        }

        return response()->json([
            'success' => true,
            'message' => 'OrderLines retrieved successfully',
            'data' => $orderLine->get(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:order,id',
            'item_type' => 'required|in:TICKET,PRODUCT',
            'seat_id' => 'nullable|exists:seat,id',
            'product_id' => 'nullable|exists:product,id',
            'qty' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        try {
            if ($request->item_type === 'TICKET' && !$request->has('seat_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'seat_id is required for TICKET item type',
                ], 400);
            }

            if ($request->item_type === 'PRODUCT' && !$request->has('product_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'product_id is required for PRODUCT item type',
                ], 400);
            }

            $order = Order::find($request->order_id);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            // tinh thanh tien
            $lineTotal = $request->unit_price * $request->qty;

            $orderLine = new OrderLine();
            $orderLine->order_id = $request->order_id;
            $orderLine->item_type = $request->item_type;
            $orderLine->seat_id = $request->seat_id;
            $orderLine->product_id = $request->product_id;
            $orderLine->qty = $request->qty;
            $orderLine->unit_price = $request->unit_price;
            $orderLine->line_total = $lineTotal;
            $orderLine->save();

            // cap nhat tong don hang
            $this->updateOrderTotal($request->order_id);

            return response()->json([
                'success' => true,
                'message' => 'OrderLine has been created successfully',
                'data' => [
                    'order_line' => $orderLine->load(['order', 'product', 'seat']),
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OrderLine has been created failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderLine $orderLine)
    {
        return response()->json([
            'success' => true,
            'data' => $orderLine->load(['order', 'product', 'seat']),
            'message' => 'OrderLine has been retrieved successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $orderLine = OrderLine::find($id);

        if ($orderLine == null) {
            return response()->json([
                'success' => false,
                'message' => "OrderLine not exist",
            ], 404);
        }

        $request->validate([
            'qty' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        try {
            // tinh thanh tien
            $lineTotal = $request->unit_price * $request->qty;

            $orderLine->qty = $request->qty;
            $orderLine->unit_price = $request->unit_price;
            $orderLine->line_total = $lineTotal;
            $orderLine->save();

            // cap nhat tong don hang
            $this->updateOrderTotal($orderLine->order_id);

            return response()->json([
                'success' => true,
                'message' => 'OrderLine has been updated successfully',
                'data' => [
                    'order_line' => $orderLine->load(['order', 'product', 'seat']),
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orderLine = OrderLine::find($id);

        if (!$orderLine) {
            return response()->json([
                'success' => false,
                'message' => "OrderLine not exist",
            ], 404);
        }

        $orderId = $orderLine->order_id;
        $orderLine->delete();

        // cap nhat tong don hang hang
        $this->updateOrderTotal($orderId);

        return response()->json([
            'success' => true,
            'message' => "OrderLine has been deleted successfully",
            'data' => $orderLine,
        ], 200);
    }

    private function updateOrderTotal($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $total = OrderLine::where('order_id', $orderId)->sum('line_total');
            $order->total_amount = $total;
            $order->save();
        }
    }


    public function getByOrder($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $orderLines = OrderLine::where('order_id', $orderId)
            ->with(['product', 'seat'])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'OrderLines retrieved successfully',
            'data' => [
                'order' => $order,
                'order_lines' => $orderLines,
            ],
        ]);
    }


    public function statistics(Request $request)
    {
        $query = OrderLine::query();

        // Sap xep theo ngay de loc
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereHas('order', function($q) use ($request) {
                $q->whereBetween('created_at', [$request->start_date, $request->end_date]);
            });
        }

        // lay du lieu phan tich
        $totalTickets = (clone $query)->where('item_type', 'TICKET')->sum('qty');
        $totalProducts = (clone $query)->where('item_type', 'PRODUCT')->sum('qty');
        $totalRevenue = (clone $query)->sum('line_total');
        $ticketRevenue = (clone $query)->where('item_type', 'TICKET')->sum('line_total');
        $productRevenue = (clone $query)->where('item_type', 'PRODUCT')->sum('line_total');

        return response()->json([
            'success' => true,
            'message' => 'Statistics retrieved successfully',
            'data' => [
                'total_tickets_sold' => $totalTickets,
                'total_products_sold' => $totalProducts,
                'total_revenue' => $totalRevenue,
                'ticket_revenue' => $ticketRevenue,
                'product_revenue' => $productRevenue,
            ],
        ]);
    }
}
