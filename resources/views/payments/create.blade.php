@extends('layouts.app')

@section('content')


    <h1>Payments details</h1>

    <h4 class="text-center"><strong>Grand total:</strong>{{$order->total}}</h4>

    <div class="text-center mb-3">
        <form
            class="d-inline"
            method="POST"
            action="{{ route('orders.payments.store',['order'=>$order->id]) }}"
        >
            @csrf
            <button type="submit" class="btn btn-success">Pay</button>
        </form>
    </div>

@endsection
