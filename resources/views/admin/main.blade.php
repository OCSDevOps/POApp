@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <div>
        <select id="projectFilter" class="form-select">
            <option value="0">All Projects</option>
            @foreach($proj_list as $project)
                <option value="{{ $project->proj_id }}">{{ $project->proj_name }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Statistics Cards Row -->
<div class="row">
    <!-- Total PO Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Purchase Orders
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPO">
                            {{ $total_po ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending PO Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Orders
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingPO">
                            {{ $pending_po ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submitted PO Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Submitted Orders
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="submittedPO">
                            {{ $submitted_po ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RTE PO Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Ready to Export
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="rtePO">
                            {{ $rte_po ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delivery Status Row -->
<div class="row">
    <!-- Fully Received Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Fully Received
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="fullyReceived">
                            {{ $fully_received ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box-open fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Partially Received Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Partially Received
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="partiallyReceived">
                            {{ $partially_received ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Not Received Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card danger h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Not Received
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="notReceived">
                            {{ $not_received ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Receive Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Received
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalReceive">
                            {{ $total_receive ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-truck-loading fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- PO Status Chart -->
    <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-pie me-1"></i>
                Purchase Order Status
            </div>
            <div class="card-body">
                <canvas id="poStatusChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Delivery Status Chart -->
    <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar me-1"></i>
                Delivery Status
            </div>
            <div class="card-body">
                <canvas id="deliveryStatusChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

@if(isset($total_rfqs))
<!-- Supplier Dashboard Section -->
<div class="row">
    <div class="col-12">
        <h4 class="mb-3">Supplier Overview</h4>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total RFQs
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $total_rfqs ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Waiting RFQs
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $waiting_rfqs ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Items
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $total_items ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cubes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card danger h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Expiring Items
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $expiring_items ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Initialize Charts
    let poStatusChart, deliveryStatusChart;
    
    function initCharts(data) {
        // PO Status Chart
        const poCtx = document.getElementById('poStatusChart').getContext('2d');
        
        if (poStatusChart) {
            poStatusChart.destroy();
        }
        
        poStatusChart = new Chart(poCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Submitted', 'Ready to Export', 'Synced'],
                datasets: [{
                    data: [
                        data.pending_po || {{ $pending_po ?? 0 }},
                        data.submitted_po || {{ $submitted_po ?? 0 }},
                        data.rte_po || {{ $rte_po ?? 0 }},
                        data.integration_sync_po || 0
                    ],
                    backgroundColor: ['#f6c23e', '#36b9cc', '#1cc88a', '#4e73df'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Delivery Status Chart
        const deliveryCtx = document.getElementById('deliveryStatusChart').getContext('2d');
        
        if (deliveryStatusChart) {
            deliveryStatusChart.destroy();
        }
        
        deliveryStatusChart = new Chart(deliveryCtx, {
            type: 'bar',
            data: {
                labels: ['Fully Received', 'Partially Received', 'Not Received'],
                datasets: [{
                    label: 'Orders',
                    data: [
                        data.fully_received || {{ $fully_received ?? 0 }},
                        data.partially_received || {{ $partially_received ?? 0 }},
                        data.not_received || {{ $not_received ?? 0 }}
                    ],
                    backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Initialize with default data
    $(document).ready(function() {
        initCharts({});
        
        // Project filter change
        $('#projectFilter').on('change', function() {
            const projId = $(this).val();
            
            $.ajax({
                url: '{{ route("admin.dashboard.chartdata") }}',
                type: 'GET',
                data: { proj_id: projId },
                success: function(data) {
                    // Update stat cards
                    $('#totalPO').text(data.total_po);
                    $('#pendingPO').text(data.pending_po);
                    $('#submittedPO').text(data.submitted_po);
                    $('#rtePO').text(data.rte_po);
                    $('#fullyReceived').text(data.fully_received);
                    $('#partiallyReceived').text(data.partially_received);
                    $('#notReceived').text(data.not_received);
                    
                    // Update charts
                    initCharts(data);
                },
                error: function(xhr) {
                    console.error('Error fetching chart data:', xhr);
                }
            });
        });
    });
</script>
@endpush
