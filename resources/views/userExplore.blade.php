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

/* Clear filters button */
.clear-filters {
    background-color: var(--accent-light);
    color: var(--accent);
    border: none;
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
    align-self: flex-start;
}
.clear-filters:hover {
    background-color: var(--accent);
    color: #fff;
}

/* Animated cards (earthquake + extra info) */
.animated-cards {
    transition: transform 0.4s ease, opacity 0.4s ease, max-height 0.4s ease, margin 0.4s ease;
    transform-origin: top center;
    margin-bottom: 25px;
    max-height: 2000px;
    overflow: hidden;
}
.animated-cards.hide {
    opacity: 0;
    transform: translateY(-30px);
    pointer-events: none;
    max-height: 0;
    margin-bottom: 0;
    margin-top: 0;
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

/* No filter message */
.no-filter-message {
    max-width: 700px;
    margin: 40px auto;
    text-align: center;
    padding: 40px 20px;
    background: #fff;
    border-radius: 16px;
    border: 2px dashed var(--border);
}
.no-filter-message .material-icons {
    font-size: 48px;
    color: var(--gray-text);
    margin-bottom: 16px;
}
.no-filter-message h4 {
    color: var(--dark-text);
    margin-bottom: 8px;
}
.no-filter-message p {
    color: var(--gray-text);
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
                                @foreach ($uniqueLocations->take(10) as $loc)
                                    <span class="filter-chip {{ in_array($loc, explode(',', request('location') ?? '')) ? 'active' : '' }}"
                                          onclick="toggleFilter('location', '{{ $loc }}')">
                                        {{ $loc }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Accident Type --}}
                        <div class="filter-group">
                            <div class="filter-label">Accident Types</div>
                            <div class="tag-container">
                                @foreach ($uniqueAccidents->take(10) as $type)
                                    <span class="filter-chip {{ in_array($type, explode(',', request('accident_type') ?? '')) ? 'active' : '' }}"
                                          onclick="toggleFilter('accident_type', '{{ addslashes($type) }}')">
                                        {{ $type }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Date Picker --}}
                        <div class="filter-group date-picker">
                            <label class="filter-label">Filter by Date</label>
                            <input type="date" name="date" id="dateInput" value="{{ request('date') }}" onchange="applyFilters()">
                        </div>

                        {{-- Clear All Filters --}}
                        @if(request('location') || request('accident_type') || request('date') || request('q'))
                        <button type="button" class="clear-filters" onclick="clearAllFilters()">
                            Clear All Filters
                        </button>
                        @endif
                    </div>

                    {{-- Hidden inputs for filter values --}}
                    <input type="hidden" name="location" id="locationInput" value="{{ request('location') }}">
                    <input type="hidden" name="accident_type" id="accidentTypeInput" value="{{ request('accident_type') }}">
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
                    <small class="text-muted d-block mt-2">Source: USGS Earthquake Hazards Program</small>
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
            @php 
                $hasFilters = request('location') || request('accident_type') || request('date') || request('q');
            @endphp
            
            @if(!$hasFilters)
                <div class="no-filter-message">
                    <span class="material-icons">filter_alt</span>
                    <h4>Select filters to view reports</h4>
                    <p>Click the filter icon above or select location, accident type, or date to see incident reports</p>
                </div>
            @else
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
                    <p class="text-center text-muted">No reports found matching your filters.</p>
                @endforelse

                <div class="d-flex justify-content-center mt-4">{{ $posts->links() }}</div>
            @endif
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

    searchInput.on("input", toggleCardsVisibility);

    function toggleCardsVisibility() {
        const hasLocation = $("#locationInput").val().trim() !== '';
        const hasAccidentType = $("#accidentTypeInput").val().trim() !== '';
        const hasDate = $("#dateInput").val().trim() !== '';
        const hasSearch = searchInput.val().trim() !== '';
        
        if(hasSearch || hasLocation || hasAccidentType || hasDate) {
            animatedCards.addClass('hide');
        } else {
            animatedCards.removeClass('hide');
        }
    }
    toggleCardsVisibility();
});

// Toggle filter (allows deselecting)
function toggleFilter(type, value) {
    // Map filter type to input ID
    let inputId;
    if(type === 'location') {
        inputId = 'locationInput';
    } else if(type === 'accident_type') {
        inputId = 'accidentTypeInput';
    }
    
    const input = document.getElementById(inputId);
    if(!input) {
        console.error('Input not found for type:', type);
        return;
    }
    
    let values = input.value ? input.value.split(',').filter(v => v) : [];
    
    // Toggle value
    if(values.includes(value)) {
        values = values.filter(v => v !== value);
    } else {
        values.push(value);
    }
    
    input.value = values.join(',');
    
    // Update UI
    updateFilterUI();
    
    // Apply filters
    applyFilters();
}

// Update filter chip UI
function updateFilterUI() {
    const locationValues = document.getElementById('locationInput').value.split(',').filter(v => v);
    const accidentTypeValues = document.getElementById('accidentTypeInput').value.split(',').filter(v => v);
    
    // Update all filter chips
    document.querySelectorAll('.filter-chip').forEach(chip => {
        const chipText = chip.textContent.trim();
        const filterGroup = chip.closest('.filter-group');
        const filterLabel = filterGroup ? filterGroup.querySelector('.filter-label') : null;
        
        if (!filterLabel) return;
        
        const labelText = filterLabel.textContent.trim();
        
        if(labelText.includes('Location')) {
            if(locationValues.includes(chipText)) {
                chip.classList.add('active');
            } else {
                chip.classList.remove('active');
            }
        } else if(labelText.includes('Accident Type')) {
            if(accidentTypeValues.includes(chipText)) {
                chip.classList.add('active');
            } else {
                chip.classList.remove('active');
            }
        }
    });
}

// Apply filters by submitting form
function applyFilters() {
    document.getElementById('exploreForm').submit();
}

// Clear all filters
function clearAllFilters() {
    document.getElementById('locationInput').value = '';
    document.getElementById('accidentTypeInput').value = '';
    document.getElementById('dateInput').value = '';
    document.getElementById('searchInput').value = '';
    
    // Remove all active classes
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.classList.remove('active');
    });
    
    applyFilters();
}

// Fetch earthquakes
$(document).ready(function() {
    const earthquakeList = $("#earthquakeList");
    const north = 21.0, south = 4.0, east = 126.6, west = 116.9;
    
    // Get earthquakes from past 30 days with magnitude 3.0+
    const endTime = new Date().toISOString();
    const startTime = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString();
    const apiUrl = `https://earthquake.usgs.gov/fdsnws/event/1/query?format=geojson&starttime=${startTime}&endtime=${endTime}&minmagnitude=3.0&limit=5&orderby=time&minlatitude=${south}&maxlatitude=${north}&minlongitude=${west}&maxlongitude=${east}`;

    $.getJSON(apiUrl, function(data) {
        const features = data.features;
        if(!features || features.length === 0) {
            earthquakeList.html(`<p class="text-muted">No earthquakes M3.0+ recorded in the past 30 days.</p>`);
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
                </a>
            `;
        });
        earthquakeList.html(html);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Earthquake API error:', textStatus, errorThrown);
        earthquakeList.html(`<p class="text-danger">Failed to load earthquake data.</p>`);
    });

    // Fetch exchange rate (USD to PHP) using Frankfurter API
    $.getJSON('https://api.frankfurter.app/latest?from=USD&to=PHP', function(data) {
        if(data && data.rates && data.rates.PHP) {
            const rate = data.rates.PHP.toFixed(2);
            $('#exchange-rate').html(`₱${rate} PHP`);
        } else {
            $('#exchange-rate').html('Unable to load');
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Exchange rate API error:', textStatus, errorThrown);
        $('#exchange-rate').html('<span class="text-danger">Failed to load</span>');
    });

    // Fetch sunrise/sunset for Kabacan, Philippines using SunriseSunset.io API
    // Coordinates for Kabacan: 7.1167° N, 124.8333° E
    const lat = 7.1167;
    const lng = 124.8333;
    const sunApiUrl = `https://api.sunrisesunset.io/json?lat=${lat}&lng=${lng}&timezone=Asia/Manila&date=today`;
    
    $.getJSON(sunApiUrl, function(data) {
        if(data && data.status === 'OK' && data.results) {
            $('#sunrise').text(data.results.sunrise);
            $('#sunset').text(data.results.sunset);
        } else {
            $('#sunrise').text('Unable to load');
            $('#sunset').text('Unable to load');
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Sunrise/Sunset API error:', textStatus, errorThrown);
        $('#sunrise').html('<span class="text-danger">Failed to load</span>');
        $('#sunset').html('<span class="text-danger">Failed to load</span>');
    });
});
</script>
@endsection