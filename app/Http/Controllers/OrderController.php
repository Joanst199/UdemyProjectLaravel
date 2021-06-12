<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public $cartService;


    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cart = $this->cartService->getFromCookie();

        if(!isset($cart)|| $cart->products->isEmpty()){
            return redirect()
                ->back()
                ->withErrors("Your cart is empty!");
        }


        return view('orders.create')
            ->with(['cart'=>$cart]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return DB::transaction(function() use($request) {
            $user = $request->user();

                    $order = $user->orders()->create([
                        'status'=>'pending'
                    ]);

                    $cart = $this->cartService->getFromCookie();

                    $cartProductsWithQuantity = $cart
                        ->products
                        ->mapWithKeys(function($product){
                            $quantity = $product->pivot->quantity;

                            if ($product->stock < $quantity) {
                                throw ValidationException::withMessages([
                                    'product'=> "There is no enough stock for the quantity you required of {$product->title}",
                                ]);
                            }

                            $product->decrement('stock', $quantity);
                            $element[$product->id] = ['quantity' => $quantity];

                            return $element;

                    });

                    $order->products()->attach($cartProductsWithQuantity->toArray());

                    return redirect()->route('orders.payments.create', ['order'=>$order]);
        }, 5);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
