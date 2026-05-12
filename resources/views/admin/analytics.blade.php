{{-- resources/views/admin/analytics.blade.php --}}
@extends('layouts.admin')

@section('title', 'Admin Analytics')

@section('content')
<style>
    /* === THEME VARIABLES === */
    :root {
        --stat-bg: #ffffff;
        --accent: #CF0F47;
        --primary: #494ca2;
        --text-main: #111315;
        --text-muted: #666666;
        --border-color: #eeeeee;
    }

    /* Force Poppins */
    .analytics-dashboard, 
    .stat-card, 
    .chart-card, 
    .chart-list li {
        font-family: 'Poppins', sans-serif !important;
    }

    .analytics-header { margin-bottom: 24px; }
    .analytics-meta { color: var(--text-muted); font-size: 14px; margin-top: -5px; }

    /* Key Stats Grid */
    .stats-grid { 
        display: grid; 
        grid-template-columns: repeat(4, 1fr); 
        gap: 20px; 
        margin-bottom: 30px; 
    }

    .stat-card { 
        background: var(--stat-bg); 
        border-radius: 16px; 
        padding: 24px; 
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: transform 0.2s;
    }

    .stat-card:hover { transform: translateY(-3px); }

    .stat-card .label { 
        color: var(--text-muted); 
        font-weight: 600; 
        font-size: 12px; 
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 8px;
    }

    .stat-card .value { 
        font-weight: 700; 
        font-size: 24px; 
        color: var(--text-main);
    }

    /* Charts & Lists Section */
    .charts-section { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 20px; 
    }

    .chart-card { 
        background: white; 
        border-radius: 20px; 
        padding: 24px; 
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }

    .chart-card h3 { 
        margin: 0 0 20px 0; 
        font-size: 16px; 
        font-weight: 700; 
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* List Design */
    .chart-list { list-style: none; padding: 0; margin: 0; }
    .chart-list li { 
        display: flex; 
        justify-content: space-between; 
        padding: 12px 0; 
        border-bottom: 1px solid #f8f9fa; 
    }
    .chart-list li:last-child { border-bottom: none; }
    .chart-list .label { color: #444; font-weight: 500; font-size: 14px; }
    .chart-list .value { 
        background: #f0f2f5; 
        padding: 2px 10px; 
        border-radius: 20px; 
        font-size: 12px; 
        font-weight: 700; 
        color: var(--primary);
    }

    /* Progress bar for location counts (bonus touch) */
    .location-item { width: 100%; display: flex; flex-direction: column; gap: 4px; }
    .location-header { display: flex; justify-content: space-between; }

    @media (max-width: 900px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-section { grid-template-columns: 1fr; }
    }
</style>

<div class="analytics-dashboard">
    <div class="analytics-header">
        <p class="analytics-meta">Real-time data and platform activity insights.</p>
    </div>

    {{-- Key Metrics --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label"><span class="material-symbols-outlined" style="font-size: 16px;">group</span> Total Users</div>
            <div class="value">{{ number_format($totalUsers) }}</div>
        </div>
        <div class="stat-card">
            <div class="label"><span class="material-symbols-outlined" style="font-size: 16px;">bolt</span> Active (30d)</div>
            <div class="value">{{ number_format($activeUsers) }}</div>
        </div>
        <div class="stat-card">
            <div class="label"><span class="material-symbols-outlined" style="font-size: 16px;">article</span> Total Posts</div>
            <div class="value">{{ number_format($totalPosts) }}</div>
        </div>
        <div class="stat-card">
            <div class="label"><span class="material-symbols-outlined" style="font-size: 16px;">flag</span> Total Reports</div>
            <div class="value" style="color: var(--accent);">{{ number_format($totalReports) }}</div>
        </div>
    </div>

    <div class="charts-section">
        {{-- Doughnut Chart --}}
        <div class="chart-card">
            <h3><span class="material-symbols-outlined">donut_small</span> Posts by Accident Type</h3>
            <div style="max-width: 300px; margin: 0 auto;">
                <canvas id="accidentTypeChart"></canvas>
            </div>
        </div>

        {{-- Reports List --}}
        <div class="chart-card">
            <h3><span class="material-symbols-outlined">warning</span> Reports by Reason</h3>
            <ul class="chart-list">
                @forelse($reportsByReason as $reason)
                    <li>
                        <span class="label">{{ ucfirst(str_replace('_', ' ', $reason->reason)) }}</span>
                        <span class="value">{{ $reason->count }}</span>
                    </li>
                @empty
                    <li class="label">No report data available.</li>
                @endforelse
            </ul>
        </div>

        {{-- Locations List --}}
        <div class="chart-card">
            <h3><span class="material-symbols-outlined">location_on</span> Top Locations</h3>
            <ul class="chart-list">
                @forelse($topLocations as $location)
                    <li>
                        <span class="label">{{ $location->location ?: 'Unknown' }}</span>
                        <span class="value">{{ $location->count }}</span>
                    </li>
                @empty
                    <li class="label">No location data available.</li>
                @endforelse
            </ul>
        </div>

        {{-- Metrics List --}}
        <div class="chart-card">
            <h3><span class="material-symbols-outlined">leaderboard</span> Efficiency Metrics</h3>
            <ul class="chart-list">
                <li>
                    <span class="label">Engagement Rate</span>
                    <span class="value" style="background: var(--primary); color: white;">
                        {{ $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0 }}%
                    </span>
                </li>
                <li>
                    <span class="label">Reports Per Post</span>
                    <span class="value">
                        {{ $totalPosts > 0 ? round($totalReports / $totalPosts, 2) : 0 }}
                    </span>
                </li>
                <li>
                    <span class="label">System Health</span>
                    <span class="value" style="color: #17b06b;">Optimal</span>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctxA = document.getElementById('accidentTypeChart').getContext('2d');
        
        const accidentLabels = [
            @foreach ($postsByType as $type) "{{ ucfirst($type->accident_type) }}", @endforeach
        ];
        const accidentCounts = [
            @foreach ($postsByType as $type) {{ $type->count }}, @endforeach
        ];

        new Chart(ctxA, {
            type: 'doughnut',
            data: {
                labels: accidentLabels,
                datasets: [{
                    data: accidentCounts,
                    backgroundColor: [
                        '#CF0F47', // Publ Accent
                        '#494ca2', // Publ Primary
                        '#FFB703', // Warning Yellow
                        '#6DD58C', // Success Green
                        '#1482e8', // Info Blue
                        '#6f42c1'  // Purple
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 10
                }]
            },
            options: {
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: { family: 'Poppins', size: 11, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#111315',
                        padding: 12,
                        titleFont: { family: 'Poppins', size: 13 },
                        bodyFont: { family: 'Poppins', size: 12 },
                        cornerRadius: 8,
                        displayColors: false
                    }
                }
            }
        });
    });
</script>
@endsection