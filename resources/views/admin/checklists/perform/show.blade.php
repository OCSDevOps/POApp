@extends('layouts.admin')

@section('title','Checklist Detail')

@section('content')
<h4 class="mb-3">Checklist Run #{{ $performchecklist->cl_p_id }}</h4>
<div class="mb-3">
    <p class="mb-1"><strong>Checklist:</strong> {{ $performchecklist->checklist->cl_name ?? '' }}</p>
    <p class="mb-1"><strong>Equipment:</strong> {{ $performchecklist->equipment->eqm_asset_name ?? 'N/A' }}</p>
    <p class="mb-1"><strong>Date:</strong> {{ $performchecklist->cl_p_date }}</p>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Value</th>
                    <th>Notes</th>
                    <th>Attachment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($performchecklist->details as $detail)
                    <tr>
                        <td>{{ $detail->item->cli_item ?? '' }}</td>
                        <td>{{ $detail->cl_pd_cli_value }}</td>
                        <td>{{ $detail->cl_pd_cli_notes }}</td>
                        <td>
                            @if($detail->cl_pd_cli_attachment)
                                <a href="{{ asset('storage/'.$detail->cl_pd_cli_attachment) }}" target="_blank">Download</a>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
