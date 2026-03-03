@extends('layouts.app')

@section('title', 'Geographic Anomalies Map')

@section('content')
<div class="geo-anomalies py-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0"><i class="fas fa-globe"></i> Geographic Anomalies Map</h1>
                <small class="text-muted">Suspicious login activity by location (Last 30 days)</small>
            </div>
            <a href="{{ route('admin.anomalies.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="row mb-4">
            <!-- World Map -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-map"></i> Anomaly Heat Map</h5>
                    </div>
                    <div class="card-body">
                        <div id="worldMap" style="width: 100%; height: 500px; background: #f0f0f0; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                            <p class="text-muted">
                                <i class="fas fa-map"></i> Interactive map would be displayed here using GeoJSON library
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="col-lg-4">
                <!-- Countries with Most Anomalies -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Top Countries</h5>
                    </div>
                    <div class="card-body p-0">
                        @if($geoAnomalies->count() > 0)
                            <table class="table table-sm mb-0">
                                <tbody>
                                    @foreach($geoAnomalies as $geo)
                                        <tr>
                                            <td>
                                                <span class="badge" style="background-color: {{ $countryRisks->firstWhere('country', $geo->country)['color'] ?? '#6c757d' }}; font-size: 12px;">
                                                    {{ $geo->country }}
                                                </span>
                                            </td>
                                            <td class="text-end"><strong>{{ $geo->count }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <div class="progress" style="height: 4px;">
                                                    <div class="progress-bar" style="width: {{ ($geo->count / $geoAnomalies->first()->count) * 100 }}%; background-color: {{ $countryRisks->firstWhere('country', $geo->country)['color'] ?? '#6c757d' }};"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="mb-0 p-3 text-muted">No geographic data</p>
                        @endif
                    </div>
                </div>

                <!-- Risk Level Legend -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-legend"></i> Risk Levels</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-danger"></span> Critical
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-warning"></span> High
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-info"></span> Medium
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-success"></span> Low
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Cities -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-city"></i> Top Cities with Anomalies</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>City</th>
                            <th>Country</th>
                            <th>Count</th>
                            <th>Risk Level</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalCount = $topCities->sum('count');
                        @endphp
                        @forelse($topCities as $city)
                            <tr>
                                <td>
                                    <i class="fas fa-city"></i> {{ $city->city }}
                                </td>
                                <td>
                                    <i class="fas fa-map-pin"></i> {{ $city->country }}
                                </td>
                                <td><strong>{{ $city->count }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $city->max_risk === 'critical' ? 'danger' : ($city->max_risk === 'high' ? 'warning' : ($city->max_risk === 'medium' ? 'warning' : 'success')) }}">
                                        {{ ucfirst($city->max_risk) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="width: 100px;">
                                        <div class="progress-bar" style="width: {{ ($city->count / $totalCount) * 100 }}%"></div>
                                    </div>
                                    {{ number_format(($city->count / $totalCount) * 100, 1) }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No geographic data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet.js for mapping (optional enhancement) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 3px;
        font-size: 12px;
    }

    table.table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    #worldMap {
        border: 1px solid #dee2e6;
    }
</style>
@endsection
