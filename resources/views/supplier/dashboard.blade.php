@extends('supplier.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-2">Welcome, {{ auth('supplier')->user()->name ?? 'Supplier' }}!</h5>
                <p class="text-muted mb-3">Use the supplier portal to view RFQs, submit quotes, and manage your catalog.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#" class="btn btn-outline-primary btn-sm disabled">RFQs (coming soon)</a>
                    <a href="#" class="btn btn-outline-secondary btn-sm disabled">Catalog</a>
                    <a href="{{ route('supplier.profile') }}" class="btn btn-primary btn-sm">Update Profile</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2">Account Status</h6>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between">
                        <span>Email verified</span>
                        <span class="badge {{ auth('supplier')->user()?->hasVerifiedEmail() ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ auth('supplier')->user()?->hasVerifiedEmail() ? 'Yes' : 'Pending' }}
                        </span>
                    </li>
                    <li class="d-flex justify-content-between mt-2">
                        <span>Active</span>
                        <span class="badge {{ (auth('supplier')->user()->status ?? 0) === 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ (auth('supplier')->user()->status ?? 0) === 1 ? 'Enabled' : 'Disabled' }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
