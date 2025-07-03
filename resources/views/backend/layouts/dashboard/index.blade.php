@extends('backend.app')

@section('title', 'Dashboard')

@push('styles')
    <style>
        .dashboard-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .growth-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .chart-container {
            position: relative;
            height: 300px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 20px;
        }

        .table-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .table-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 20px;
        }

        .table thead th {
            border: none;
            background: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: background 0.3s;
        }

        .table tbody tr:hover {
            background: #f3f6fa;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 15px;
        }

        .welcome-time {
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
@endpush

@section('content')
    <div class="page-content wrapper">
        <div class="container-fluid">
            {{-- Welcome Header --}}
            <div class="page-header text-center">
                <h1 class="mb-2">Welcome to Admin Dashboard</h1>
                <p class="welcome-time mb-0">{{ now()->format('l, F j, Y - g:i A') }}</p>
            </div>

            {{-- Statistics Cards --}}
            <div class="row g-4 mb-5">
                @foreach ($stats as $key => $stat)
                    <div class="col-lg-3 col-md-6">
                        <div class="card dashboard-card h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-{{ $stat['color'] }} me-3">
                                        <i class="bi {{ $stat['icon'] }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="text-muted mb-1 text-uppercase" style="font-size: 0.8rem;">
                                            {{ ucfirst(str_replace('_', ' ', $key)) }}
                                        </h6>
                                        <h2 class="mb-0 fw-bold">{{ number_format($stat['total']) }}</h2>
                                        @if ($stat['growth'] != 0)
                                            <span
                                                class="growth-badge {{ $stat['growth'] > 0 ? 'bg-success' : 'bg-danger' }} text-white">
                                                <i class="bi bi-arrow-{{ $stat['growth'] > 0 ? 'up' : 'down' }}"></i>
                                                {{ abs($stat['growth']) }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if ($key === 'plans')
                                <div class="card-footer bg-transparent border-0 pt-0">
                                    <a href="{{ route('subscription-plan.index') }}" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-gear me-1"></i>Manage Plans
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Charts Section --}}
            <div class="row g-4 mb-5">
                <div class="col-lg-8">
                    <div class="chart-container">
                        <h6 class="text-muted mb-3">Last 7 Days Activity</h6>
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card dashboard-card h-100">
                        <div class="card-header bg-gradient text-white">
                            <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>System Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="system-info">
                                <div class="info-item mb-3">
                                    <small class="text-muted d-block">System Name</small>
                                    <strong>{{ $systemInfo->system_name ?? 'N/A' }}</strong>
                                </div>
                                <div class="info-item mb-3">
                                    <small class="text-muted d-block">Email</small>
                                    <strong>{{ $systemInfo->email ?? 'N/A' }}</strong>
                                </div>
                                <div class="info-item mb-3">
                                    <small class="text-muted d-block">Phone</small>
                                    <strong>{{ $systemInfo->phone_number ?? 'N/A' }}</strong>
                                </div>
                                <div class="info-item mb-3">
                                    <small class="text-muted d-block">Address</small>
                                    <strong>{{ $systemInfo->address ?? 'N/A' }}</strong>
                                </div>
                                <div class="info-item">
                                    <small class="text-muted d-block">Copyright</small>
                                    <strong>{{ $systemInfo->copyright_text ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity Tables --}}
            <div class="row g-4">
                {{-- Recent Users --}}
                <div class="col-lg-12 mb-4">
                    <div class="card table-card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-people me-2"></i>Recent Users</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th><i class="bi bi-person me-1"></i>Name</th>
                                            <th><i class="bi bi-envelope me-1"></i>Email</th>
                                            <th><i class="bi bi-calendar me-1"></i>Registered</th>
                                            <th><i class="bi bi-check-circle me-1"></i>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentUsers as $user)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </div>
                                                        <strong>{{ $user->name }}</strong>
                                                    </div>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <span
                                                        class="status-badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }} text-white">
                                                        {{ ucfirst($user->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    No users found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Products --}}
                <div class="col-lg-12 mb-4">
                    <div class="card table-card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Recent Products</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th><i class="bi bi-box me-1"></i>Product</th>
                                            <th><i class="bi bi-person me-1"></i>User</th>
                                            <th><i class="bi bi-tag me-1"></i>Category</th>
                                            <th><i class="bi bi-calendar me-1"></i>Created</th>
                                            <th><i class="bi bi-check-circle me-1"></i>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentProducts as $product)
                                            <tr>
                                                <td><strong>{{ $product->name }}</strong></td>
                                                <td>{{ $product->user->name ?? '-' }}</td>
                                                <td>{{ $product->category->name ?? '-' }}</td>
                                                <td>{{ $product->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <span
                                                        class="status-badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }} text-white">
                                                        {{ ucfirst($product->status ?? 'N/A') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    No products found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Subscriptions --}}
                <div class="col-lg-12 mb-4">
                    <div class="card table-card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Recent Subscriptions</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th><i class="bi bi-person me-1"></i>User</th>
                                            <th><i class="bi bi-card-list me-1"></i>Plan</th>
                                            <th><i class="bi bi-check-circle me-1"></i>Status</th>
                                            <th><i class="bi bi-calendar-check me-1"></i>Started</th>
                                            <th><i class="bi bi-calendar-x me-1"></i>Ends</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentSubscriptions as $sub)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="avatar-sm bg-info text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                            {{ strtoupper(substr($sub->user->name ?? 'N', 0, 1)) }}
                                                        </div>
                                                        <strong>{{ $sub->user->name ?? '-' }}</strong>
                                                    </div>
                                                </td>
                                                <td>{{ $sub->subscriptionPlan->name ?? '-' }}</td>
                                                <td>
                                                    <span
                                                        class="status-badge bg-{{ $sub->status === 'active' ? 'success' : ($sub->status === 'expired' ? 'danger' : 'secondary') }} text-white">
                                                        {{ ucfirst($sub->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $sub->starts_at ? $sub->starts_at->format('M d, Y') : '-' }}</td>
                                                <td>{{ $sub->ends_at ? $sub->ends_at->format('M d, Y') : '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    No subscriptions found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Activity Chart
        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'New Users',
                    data: @json($chartData['users']),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'New Products',
                    data: @json($chartData['products']),
                    borderColor: '#764ba2',
                    backgroundColor: 'rgba(118, 75, 162, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    </script>
@endpush
