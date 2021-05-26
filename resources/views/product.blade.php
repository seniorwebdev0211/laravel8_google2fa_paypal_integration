@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <form class="form-horizontal" method="GET" action="{{ route('make.payment') }}">
                <h2>Product 1</h2>
                <p>Description for product 1</p>
                <p>Price: $100.55</p>
                <input type="hidden" name="amount" value="100.55">
                <input type="hidden" name="currency" value="USD">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        Pay $100.55
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <form class="form-horizontal" method="GET" action="{{ route('make.payment') }}">
                <h2>Product 2</h2>
                <p>Description for product 2</p>
                <p>Price: $200.55</p>
                <input type="hidden" name="amount" value="200.55">
                <input type="hidden" name="currency" value="USD">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        Pay $200.55
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <form class="form-horizontal" method="GET" action="{{ route('make.payment') }}">
                <h2>Product 3</h2>
                <p>Description for product 3</p>
                <p>Price: $300.55</p>
                <input type="hidden" name="amount" value="300.55">
                <input type="hidden" name="currency" value="USD">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        Pay $300.55
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection