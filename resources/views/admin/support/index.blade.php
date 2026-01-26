@extends('layouts.admin')

@section('title','Support')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <h4 class="mb-1">Support & Reminders</h4>
        <p class="text-muted mb-0">Operational support tools and scheduled reminders.</p>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6>Price Expiry Reminder</h6>
                <p class="text-muted mb-2">Run the price-expiry reminder command to notify suppliers of catalog pricing nearing expiry.</p>
                <code>php artisan reminders:price-expiry</code>
            </div>
        </div>
    </div>
</div>
@endsection
