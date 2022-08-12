<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Detail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function lists(Request $request){
        $orders = Order::join('details', 'orders.id', '=', 'details.order_id')
            ->orderBy('transaction_date','desc')
            ->groupBy('transaction_date', 'status', 'id')
            ->select(DB::raw('count(*) AS total_items'), 'transaction_date', 'status', 'id');
        if ($request->status == 'completed')
            $orders = $orders->where('status', '=', 'completed');
        else if ($request->status == 'ordered')
            $orders = $orders->where('status', '=', 'ordered');
        else if ($request->status == 'not ordered')
            $orders = $orders->where('status', '=', 'not ordered');
        $orders = $orders->get();
        return response(['data' => $orders], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Order::create([
            'staff_id' => auth()->user()->id,
            'transaction_date' => DB::raw('NOW()'),
            'arrived_at' => DB::raw('NULL'),
            'status' => DB::raw('NULL')
        ]);
        return response(['message' => 'order created successfully'], 200);
    }

    public function add_order(Request $request){
        $order = Order::where('status', '=', 'not ordered')->orderBy('transaction_date', 'desc')->first();
        if ($order == null){
            $uuid = Str::uuid()->toString();
            while (Order::find($uuid) != null)
                $uuid = Str::uuid()->toString();
            $order = Order::create([
                'id' => $uuid,
                'staff_id' => auth()->user()->id,
                'transaction_date' => DB::raw('NOW()'),
                'arrived_at' => DB::raw('NULL'),
                'status' => 'not ordered'
            ]);
        }
        $data = $request->json()->all();
        Detail::create([
            'order_id' => $order->id,
            'book_id' => $data['book_id'],
            'quantity' => $data['quantity']
        ]);
        return response(['message' => 'added items to order'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::find($id);
        $books = $order
            ->join('details', 'details.order_id', '=', 'orders.id')
            ->join('books', 'books.id', '=', 'details.book_id')
            ->where('orders.id', '=', $id)
            ->get();
        if ($order == null)
            return response(['message' => 'order not found'], 404);
        else
            return response([
                'order' => $order,
                'books' => $books
            ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        $data = $request->json()->all();
        $validator = Validator::make($data, [
            'status' => 'required'
        ]);
        if ($validator->fails()){
            return response(['message' => $validator->errors()->first()], 422);
        }
        $order = Order::find($id);
        if ($order == null)
            return response(['message' => 'order not found'], 404);

        $order->status = $data['status'];
        if ($data['status'] == 'completed'){
            $order->arrived_at = DB::raw('NOW()');
            foreach($order->details as $detail){
                $b = Book::find($detail->book_id);
                $b->stock -= $detail->quantity;
                $b->save();
            }
        }
        $order->save();
        return response(['message' => 'status changed successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        if ($order == null)
            return response(['message' => 'order not found'], 404);

        $order->delete();
        return response(['message' => 'order deleted sucessfully'], 404);
    }
}
