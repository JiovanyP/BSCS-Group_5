{{-- resources/views/admin/analytics.blade.php --}}
@extends('layouts.admin')

@section('title', 'Admin Analytics')

@section('content')
<style>
/* Reuse admin dashboard variables for consistency */
:root {
  --bg: #071018;
  --panel: rgba(255,255,255,0.02);
  --muted: #98a0a8;
  --accent: #CF0F47;
  --green: #17b06b;
  --red: #ea4d4d;
  --blue: #1482e8;
  --card-radius: 12px;
  --card-shadow: 0 10px 30px rgba(0,0,0,0.6);
}

/* Analytics layout */
.analytics-dashboard { display:block; }
.analytics-header { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:18px; }
.analytics-title h1 { margin:0; font-size:20px; }
.analytics-meta { color:var(--muted); margin-top:6px; font-size:13px; }

/* Stats grid */
.stats-grid { display:grid; grid-template-columns: repeat(4,1fr); gap:12px; margin-bottom:16px; }
.stat-card { background:var(--panel); border-radius:10px; padding:12px; box-shadow:var(--card-shadow); text-align:center; }
.stat-card .label { color:var(--muted); font-weight:700; font-size:13px; }
.stat-card .value { font-weight:800; font-size:18px; margin-top:6px; }

/* Charts and lists */
.charts-section { display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:16px; }
.chart-card { background:var(--panel); border-radius:12px; padding:14px; box-shadow:var(--card-shadow); }
.chart-card h3 { margin:0 0 10px 0; color:#eaf2fa; font-size:16px; }
.chart-list { list-style:none; padding:0; margin:0; }
.chart-list li { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.02); }
.chart-list li:last-child { border-bottom:none; }
.chart-list .label { color:#eaf2fa; }
.chart-list .value { color:var(--muted); font-weight:700; }

/* Accident types list */

/* Responsive */
@media (max-width:720px) {
  .stats-grid { grid-template-columns: repeat(2,1fr); }
  .charts-section { grid-template-columns: 1fr; }
  .accident-types-grid { grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); }
}
</style>

<div class="analytics-dashboard">
  <div class="analytics-header">
    <div class="analytics-title">
      <h1>Analytics — Overview</h1>
      <div class="analytics-meta">Comprehensive insights into platform activity and user engagement</div>
    </div>
  </div>

  <div class="stats-grid" role="region" aria-label="Key metrics">
    <div class="stat-card" aria-label="Total Users">
      <div class="label">Total Users</div>
      <div class="value">{{ number_format($totalUsers) }}</div>
    </div>
    <div class="stat-card" aria-label="Active Users">
      <div class="label">Active Users (30d)</div>
      <div class="value">{{ number_format($activeUsers) }}</div>
    </div>
    <div class="stat-card" aria-label="Total Posts">
      <div class="label">Total Posts</div>
      <div class="value">{{ number_format($totalPosts) }}</div>
    </div>
    <div class="stat-card" aria-label="Total Reports">
      <div class="label">Total Reports</div>
      <div class="value">{{ number_format($totalReports) }}</div>
    </div>
  </div>

  <div class="charts-section">
    <div class="chart-card">
      <h3>Posts by Accident Type</h3>
      <ul class="chart-list">
        @forelse($postsByType as $type)
          <li>
            <span class="label">{{ ucfirst($type->accident_type) }}</span>
            <span class="value">{{ $type->count }}</span>
          </li>
        @empty
          <li><span class="label">No data available</span></li>
        @endforelse
      </ul>
    </div>

    <div class="chart-card">
      <h3>Reports by Reason</h3>
      <ul class="chart-list">
        @forelse($reportsByReason as $reason)
          <li>
            <span class="label">{{ ucfirst(str_replace('_', ' ', $reason->reason)) }}</span>
            <span class="value">{{ $reason->count }}</span>
          </li>
        @empty
          <li><span class="label">No data available</span></li>
        @endforelse
      </ul>
    </div>

    <div class="chart-card">
      <h3>Top Locations by Posts</h3>
      <ul class="chart-list">
        @forelse($topLocations as $location)
          <li>
            <span class="label">{{ $location->location ?: 'Unknown' }}</span>
            <span class="value">{{ $location->count }}</span>
          </li>
        @empty
          <li><span class="label">No data available</span></li>
        @endforelse
      </ul>
    </div>

    <div class="chart-card">
      <h3>Additional Metrics</h3>
      <ul class="chart-list">
        <li>
          <span class="label">User Engagement Rate</span>
          <span class="value">{{ $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0 }}%</span>
        </li>
        <li>
          <span class="label">Reports per Post</span>
          <span class="value">{{ $totalPosts > 0 ? round($totalReports / $totalPosts, 2) : 0 }}</span>
        </li>
      </ul>
    </div>
  </div>
</div>


@endsection
