@extends('layouts.admin')

@section('title', 'Supplier Performance')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <h4 class="mb-1">Performance - {{ $supplier->sup_name ?? '' }}</h4>
        <p class="text-muted mb-0">Quality and delivery performance based on historical POs.</p>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">KPIs</div>
            <div class="card-body">
                @if($performance)
                    <p><strong>On-time Delivery:</strong> {{ $performance->on_time_percentage ?? 'N/A' }}%</p>
                    <p><strong>Average Lead Time:</strong> {{ $performance->avg_lead_time ?? 'N/A' }} days</p>
                    <p><strong>Total Orders:</strong> {{ $performance->total_orders ?? 0 }}</p>
                    <p><strong>Quality Score:</strong> {{ $performance->quality_score ?? 'N/A' }}</p>
                @else
                    <p class="text-muted">No performance data available.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">Order History (last 12 months)</div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Orders</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orderHistory as $row)
                            <tr>
                                <td>{{ $row->year }}</td>
                                <td>{{ $row->month }}</td>
                                <td>{{ $row->order_count }}</td>
                                <td>{{ $row->total_amount }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
