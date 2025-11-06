@extends('layouts.admin')

@section('title', 'Reported Posts')

@section('content')
<div class="admin-page">
    <div class="page-header">
        <h1>Reported Posts</h1>
        <p class="meta">View and manage reports submitted by users.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($reports->isEmpty())
        <div class="empty-state">
            <p>No reports found 🎉</p>
        </div>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Post</th>
                    <th>Reported By</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $report)
                    <tr>
                        <td>{{ $report->id }}</td>
                        <td>
                            <a href="{{ route('admin.posts.view', $report->post_id) }}" target="_blank">
                                {{ Str::limit($report->post->title ?? 'Deleted Post', 40) }}
                            </a>
                        </td>
                        <td>{{ $report->user->name ?? 'Unknown' }}</td>
                        <td>{{ $report->reason ?? 'N/A' }}</td>
                        <td>
                            @if ($report->resolved)
                                <span class="status resolved">Resolved</span>
                            @else
                                <span class="status pending">Pending</span>
                            @endif
                        </td>
                        <td>{{ $report->created_at->format('M d, Y') }}</td>
                        <td>
                            @if (!$report->resolved)
                                <form method="POST" action="{{ route('admin.reports.resolve', $report->post_id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Resolve</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.reports.resolveOrphan') }}">
                                    @csrf
                                    <input type="hidden" name="report_id" value="{{ $report->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Reopen</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<style>
.admin-page {
    padding: 20px;
}
.page-header {
    margin-bottom: 20px;
}
.page-header h1 {
    margin: 0;
}
.admin-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
}
.admin-table th, .admin-table td {
    padding: 10px 14px;
    border-bottom: 1px solid #eee;
    text-align: left;
}
.admin-table th {
    background: #f7f7f7;
}
.status.resolved {
    color: #28a745;
    font-weight: bold;
}
.status.pending {
    color: #dc3545;
    font-weight: bold;
}
.btn {
    border: none;
    padding: 6px 10px;
    border-radius: 4px;
    cursor: pointer;
}
.btn-success {
    background: #28a745;
    color: #fff;
}
.btn-outline-secondary {
    border: 1px solid #999;
    color: #333;
    background: none;
}
.alert-success {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 6px;
}
.empty-state {
    text-align: center;
    padding: 50px 0;
    font-size: 1.1rem;
    color: #777;
}
</style>
@endsection
