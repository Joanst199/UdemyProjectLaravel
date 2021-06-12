@extends('layouts.app')

@section('content')


    <h1>Order details</h1>

    <h4 class="text-center"><strong>Grand total:</strong>{{$cart->total}}</h4>

    <div class="text-center mb-3">
        <form
            class="d-inline"
            method="POST"
            action="{{ route('orders.store') }}"
        >
            @csrf
            <button type="submit" class="btn btn-success">Confirm order</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="thead-light">
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($cart->products as $product)

                <tr>
                    <td>
                        <img src="{{asset($product->images->first()->path)}}" alt="" width="100px">
                        {{$product->title}}
                    </td>
                    <td>{{$product->price}}</td>
                    <td>{{$product->pivot->quantity}}</td>
                    <td>
                        <strong>
                            ${{$product->total}}
                        </strong>
                    </td>

                </tr>

                @endforeach
            </tbody>
        </table>
    </div>
@endsection
