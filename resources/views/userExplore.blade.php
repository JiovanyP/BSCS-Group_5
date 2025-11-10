@extends('layouts.app')

@section('title', 'Explore')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
:root {
    --primary: #494ca2;
    --accent: #CF0F47;
    --accent-2: #FF0B55;
    --accent-light: #fbebf1;
    --dark-text: #333;
    --gray-text: #919191;
    --muted: #e9e9ed;
    --border: #e0e0e0;
}

/* Main content */
.main-content {
    background-color: #fafafa;
    padding: 30px 0;
}

/* Sticky search container */
.search-container-wrapper {
    position: sticky;
    top: 0;
    background: #fafafa;
    z-index: 20;
    padding: 10px 0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

/* Search field */
.search-container {
    background: #fff;
    border-radius: 50px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid var(--border);
    padding: 6px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    max-width: 700px;
    margin: 0 auto;
}

.search-container input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-size: 15px;
    padding: 10px 14px;
    color: var(--dark-text);
}

.search-container button {
    border: none;
    border-radius: 25px;
    padding: 8px 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
    color: #9a9a9aff;
    background: transparent;
}

.search-container button.active,
.search-container button:hover {
    background-color: #fbebf1;
    color: #ff0b55;
}

/* Filter toggle */
.filter-toggle {
    border: none;
    background: transparent;
    color: var(--muted);
    cursor: pointer;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.3s;
}
.filter-toggle:hover {
    background-color: var(--accent-light);
    color: var(--accent);
}

/* Filter panel */
.filter-panel {
    display: none;
    flex-direction: column;
    gap: 16px;
    margin: 10px auto 20px;
    max-width: 700px;
    transition: max-height 0.3s ease, opacity 0.3s ease;
    opacity: 0;
    overflow: hidden;
}
.filter-panel.active {
    display: flex;
    opacity: 1;
}

/* Filter chips */
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.filter-label {
    font-weight: 600;
    color: var(--gray-text);
    font-size: 14px;
}
.tag-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.filter-chip {
    border-radius: 25px;
    padding: 6px 14px;
    font-size: 14px;
    background-color: var(--muted);
    color: var(--gray-text);
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
}
.filter-chip:hover {
    background-color: var(--accent-light);
    color: var(--accent);
}
.filter-chip.active {
    background-color: var(--accent);
    color: #fff;
}

/* Date picker */
.date-picker input[type="date"] {
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 8px 14px;
    font-size: 14px;
    outline: none;
    transition: 0.2s;
}
.date-picker input[type="date"]:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(207, 15, 71, 0.15);
}

/* Animated cards (earthquake + extra info) */
.animated-cards {
    transition: transform 0.4s ease, opacity 0.4s ease;
    transform-origin: top center;
    margin-bottom: 25px;
}
.animated-cards.hide {
    opacity: 0;
    transform: translateY(-30px);
    pointer-events: none;
}

/* Earthquake section */
.earthquake-section {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 14px 18px;
    max-width: 700px;
    margin: 10px auto;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
.earthquake-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.earthquake-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-radius: 12px;
    background-color: #fafafa;
    border: 1px solid #eee;
    transition: transform 0.2s, box-shadow 0.2s, background-color 0.2s;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}
.earthquake-item:hover {
    background-color: #fbebf1;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.eq-mag { font-weight: 700; color: var(--accent); }
.eq-loc { flex: 1; margin-left: 10px; color: var(--gray-text); font-size: 14px; }
.eq-time { font-size: 13px; color: var(--gray-text); }

/* Extra info cards */
.extra-info-cards {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin: 10px auto;
    max-width: 700px;
}
.extra-info-card {
    flex: 1;
    min-width: 280px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
.extra-info-card h5 {
    color: var(--accent);
    font-weight: 700;
    margin-bottom: 10px;
}
/* Timeline label styling */
.timeline-label {
    background: #fff;
    color: var(--accent);
    font-weight: 700;
    display: inline-block;
    padding: 8px 20px;
    border-radius: 25px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    margin: 20px 0 10px;
    font-size: 14px;
    text-align: center;
}

</style>

<div class="main-content">
    <div class="container">
        <div class="col-xl-8 mx-auto">

            {{-- Search + Filter --}}
            <div class="search-container-wrapper">
                <form method="GET" action="{{ route('userExplore') }}" id="exploreForm">
                    <div class="search-container">
                        <button type="button" class="filter-toggle" id="filterBtn" title="Toggle Filters">
                            <span class="material-icons">tune</span>
                        </button>
                        <input type="text" name="q" id="searchInput" placeholder="Search incidents, locations, or types..." value="{{ request('q') }}">
                        <button type="submit" id="searchBtn">Search</button>
                    </div>

                    {{-- Filters --}}
                    <div class="filter-panel" id="filterPanel">
                        {{-- Location --}}
                        <div class="filter-group">
                            <div class="filter-label">Popular Locations</div>
                            <div class="tag-container">
                                <span class="filter-chip {{ request('location') == null ? 'active' : '' }}" onclick="selectTag('location', '', true)">All</span>
                                @foreach ($uniqueLocations->take(10) as $loc)
                                    <span class="filter-chip {{ in_array($loc, explode(',', request('location') ?? '')) ? 'active' : '' }}"
                                          onclick="selectTag('location', '{{ $loc }}', true)">
                                        {{ $loc }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Accident Type --}}
                        <div class="filter-group">
                            <div class="filter-label">Accident Types</div>
                            <div class="tag-container">
                                <span class="filter-chip {{ request('accident_type') == null ? 'active' : '' }}" onclick="selectTag('accident_type', '', true)">All</span>
                                @foreach ($uniqueAccidents->take(10) as $type)
                                    <span class="filter-chip {{ in_array($type, explode(',', request('accident_type') ?? '')) ? 'active' : '' }}"
                                          onclick="selectTag('accident_type', '{{ $type }}', true)">
                                        {{ $type }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Date Picker --}}
                        <div class="filter-group date-picker">
                            <label class="filter-label">Filter by Date</label>
                            <input type="date" name="date" value="{{ request('date') }}" onchange="document.getElementById('exploreForm').submit()">
                        </div>
                    </div>
                </form>
            </div>

            {{-- Animated Cards separated from search/filter --}}
            <div class="animated-cards" id="animatedCards">
                {{-- Earthquake Section --}}
                <div class="earthquake-section mt-3" id="earthquakeSection">
                    <div class="filter-label mb-2">Recent Earthquakes (Philippines)</div>
                    <div id="earthquakeList" class="earthquake-list">
                        <p class="text-muted">Loading recent earthquake data...</p>
                    </div>
                </div>

                {{-- Extra Info --}}
                <div class="extra-info-cards">
                    <div class="extra-info-card">
                        <h5>💱 USD → PHP Exchange Rate</h5>
                        <p id="exchange-rate" style="font-size: 18px; font-weight: bold; color: var(--dark-text);">Loading...</p>
                        <small class="text-muted">Source: latest.currency-api.pages.dev</small>
                    </div>
                    <div class="extra-info-card">
                        <h5>🌅 Kabacan Sunrise & Sunset</h5>
                        <p><strong>Sunrise:</strong> <span id="sunrise">Loading...</span></p>
                        <p><strong>Sunset:</strong> <span id="sunset">Loading...</span></p>
                        <small class="text-muted">Location: Kabacan, Philippines</small>
                    </div>
                </div>
            </div>

            {{-- Timeline Posts --}}
            @php $currentDate = null; @endphp
            @forelse ($posts as $post)
                @if ($currentDate !== $post->created_at->toDateString())
                    <div class="timeline-label text-center font-weight-bold my-3">
                        {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                    </div>
                    @php $currentDate = $post->created_at->toDateString(); @endphp
                @endif
                @include('partials.post', ['post' => $post])
            @empty
                <p class="text-center text-muted">No reports yet.</p>
            @endforelse

            <div class="d-flex justify-content-center mt-4">{{ $posts->links() }}</div>
        </div>
    </div>
</div>

@include('partials.delete-report-modals')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/post-interactions.js') }}"></script>

<script>
$(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const filterPanel = $("#filterPanel");
    const filterBtn = $("#filterBtn");
    const searchInput = $("#searchInput");
    const animatedCards = $("#animatedCards");

    filterBtn.on('click', function() {
        filterPanel.toggleClass('active');
    });

    $(".filter-chip").on("click", function() {
        setTimeout(toggleCardsVisibility, 50);
    });

    searchInput.on("input", toggleCardsVisibility);

    function toggleCardsVisibility() {
        const activeTags = $(".filter-chip.active").not(':contains("All")').length;
        const hasSearch = searchInput.val().trim() !== '';
        if(hasSearch || activeTags > 0) animatedCards.addClass('hide');
        else animatedCards.removeClass('hide');
    }
    toggleCardsVisibility();
});

// Multi-select tags with All
function selectTag(type, value, keepOpen = false) {
    const form = document.getElementById('exploreForm');
    let input = form.querySelector(`[name="${type}"]`);
    let values = [];
    if(!input) { input = document.createElement('input'); input.type = 'hidden'; input.name = type; form.appendChild(input); }
    else values = input.value ? input.value.split(',') : [];

    if(value === '') {
        values = [];
        document.querySelectorAll(`.filter-chip`).forEach(chip => {
            if(chip.textContent.trim() === "All") chip.classList.add('active');
            else chip.classList.remove('active');
        });
    } else {
        if(values.includes(value)) values = values.filter(v => v !== value);
        else values.push(value);
        document.querySelectorAll(`.filter-chip`).forEach(chip => {
            if(chip.textContent.trim() === value) chip.classList.toggle('active');
            else if(chip.textContent.trim() === "All") chip.classList.remove('active');
        });
    }

    input.value = values.join(',');
    form.submit();

    if(keepOpen) document.getElementById('filterPanel').classList.add('active');
}

// Fetch earthquakes
$(document).ready(function() {
    const earthquakeList = $("#earthquakeList");
    const north = 21.0, south = 4.0, east = 126.6, west = 116.9;
    const apiUrl = `https://earthquake.usgs.gov/fdsnws/event/1/query?format=geojson&minmagnitude=3&limit=3&orderby=time&minlatitude=${south}&maxlatitude=${north}&minlongitude=${west}&maxlongitude=${east}`;

    $.getJSON(apiUrl, function(data) {
        const features = data.features;
        if(!features || features.length === 0) {
            earthquakeList.html(`<p class="text-muted">No recent earthquakes recorded.</p>`);
            return;
        }
        let html = "";
        features.forEach(eq => {
            const mag = eq.properties.mag.toFixed(1);
            const place = eq.properties.place;
            const time = new Date(eq.properties.time).toLocaleString("en-PH", {
                hour12: true, month: "short", day: "numeric", hour: "2-digit", minute: "2-digit"
            });
            const url = eq.properties.url;
            html += `
                <a href="${url}" target="_blank" class="earthquake-item">
                    <span class="eq-mag">M${mag}</span>
                    <span class="eq-loc">${place}</span>
                    <span class="eq-time">${time}</span>
                    <small>Source: USGS</small>
                </a>
            `;
        });
        earthquakeList.html(html);
    }).fail(function() {
        earthquakeList.html(`<p class="text-danger">Failed to load earthquake data.</p>`);
    });
});
</script>
@endsection
