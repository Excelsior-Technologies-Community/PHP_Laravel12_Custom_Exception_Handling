<!DOCTYPE html>
<html lang="en" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exception Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --bg: #f0f2f8;
            --card: #ffffff;
            --text: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --header-bg: linear-gradient(135deg, #4f46e5, #7c3aed);
        }
        [data-theme="dark"] {
            --bg: #0f172a;
            --card: #1e293b;
            --text: #e2e8f0;
            --muted: #94a3b8;
            --border: #334155;
        }
        body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', sans-serif; transition: .3s; }

        .dashboard-header {
            background: var(--header-bg);
            color: white;
            padding: 32px 40px;
            border-radius: 20px;
            margin-bottom: 24px;
            box-shadow: 0 12px 35px rgba(79,70,229,.3);
        }

        .stats-card {
            background: var(--card);
            border-radius: 16px;
            padding: 20px 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
            border-left: 5px solid var(--primary);
            transition: .25s;
        }
        .stats-card:hover { transform: translateY(-4px); }
        .stats-number { font-size: 30px; font-weight: 800; }

        .filter-card, .table-card, .chart-card {
            background: var(--card);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
            margin-bottom: 20px;
        }

        .table thead { background: #1e293b; color: white; }
        [data-theme="dark"] .table thead { background: #0f172a; }
        .table tbody tr:hover { background: rgba(99,102,241,.05); }
        .table td, .table th { vertical-align: middle; color: var(--text); }
        [data-theme="dark"] .table { color: var(--text); }
        [data-theme="dark"] .table-striped > tbody > tr:nth-of-type(odd) > * { background: rgba(255,255,255,.03); }

        .badge-severity-low      { background: #22c55e; color: white; }
        .badge-severity-medium   { background: #f59e0b; color: white; }
        .badge-severity-high     { background: #ef4444; color: white; }
        .badge-severity-critical { background: #1e293b; color: white; }

        .badge-status-open          { background: #ef4444; color: white; }
        .badge-status-investigating { background: #f59e0b; color: white; }
        .badge-status-resolved      { background: #22c55e; color: white; }

        .badge-type { background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; }

        .badge-pill { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }

        .btn-primary-custom {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
        }
        .btn-primary-custom:hover { background: var(--primary-dark); color: white; }

        .form-control, .form-select {
            background: var(--card);
            color: var(--text);
            border-color: var(--border);
            border-radius: 10px;
        }
        .form-control:focus, .form-select:focus {
            background: var(--card);
            color: var(--text);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }

        .pagination .page-link { border-radius: 8px; margin: 0 3px; color: var(--primary); background: var(--card); border-color: var(--border); }
        .pagination .active .page-link { background: var(--primary); border-color: var(--primary); color: white; }

        .modal-content { background: var(--card); color: var(--text); border-radius: 16px; }
        .modal-header { border-bottom-color: var(--border); }
        .modal-footer { border-top-color: var(--border); }

        .stack-trace {
            background: #0f172a;
            color: #a5f3fc;
            font-size: 12px;
            border-radius: 10px;
            padding: 16px;
            max-height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
        }

        .dark-toggle { cursor: pointer; font-size: 20px; }
        .empty-box { text-align: center; padding: 50px; color: var(--muted); }

        @media (max-width: 768px) {
            .dashboard-header { padding: 20px; }
            .stats-number { font-size: 22px; }
            .table-responsive { font-size: 13px; }
        }
    </style>
</head>
<body>
<div class="container-fluid px-4 py-4">

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="dashboard-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-shield-exclamation me-2"></i>Exception Monitoring Dashboard</h3>
            <p class="mb-0 opacity-75">Real-time exception tracking with analytics, filters & export</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="dark-toggle text-white" onclick="toggleDark()" title="Toggle Dark Mode">
                <i class="bi bi-moon-stars-fill" id="darkIcon"></i>
            </span>
            <a href="{{ url('/exception') }}" class="btn btn-light btn-sm rounded-pill">
                <i class="bi bi-bug me-1"></i>Test Exception
            </a>
        </div>
    </div>

    {{-- STATS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stats-card">
                <div class="text-muted small mb-1"><i class="bi bi-collection me-1"></i>Total</div>
                <div class="stats-number" style="color:#6366f1">{{ $totalExceptions }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stats-card" style="border-left-color:#06b6d4">
                <div class="text-muted small mb-1"><i class="bi bi-calendar-day me-1"></i>Today</div>
                <div class="stats-number" style="color:#06b6d4">{{ $todayExceptions }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stats-card" style="border-left-color:#22c55e">
                <div class="text-muted small mb-1"><i class="bi bi-check2-circle me-1"></i>Resolved</div>
                <div class="stats-number" style="color:#22c55e">{{ $resolvedCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stats-card" style="border-left-color:#ef4444">
                <div class="text-muted small mb-1"><i class="bi bi-lightning me-1"></i>Latest Type</div>
                <div class="fw-bold" style="color:#ef4444;font-size:14px;margin-top:6px">
                    {{ $latestException->exception_type ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="chart-card">
                <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2"></i>Last 7 Days Trend</h6>
                <canvas id="trendChart" height="100"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="chart-card">
                <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2"></i>Severity Breakdown</h6>
                <canvas id="severityChart" height="180"></canvas>
            </div>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="filter-card">
        <form method="GET" action="{{ url('/exception-history') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="🔍 Search message, URL, IP..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="severity" class="form-select">
                        <option value="">All Severities</option>
                        @foreach(['low','medium','high','critical'] as $s)
                            <option value="{{ $s }}" {{ request('severity') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['open','investigating','resolved'] as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="exception_type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($exceptionTypes as $type)
                            <option value="{{ $type }}" {{ request('exception_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="From Date">
                </div>
                <div class="col-md-1">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" title="To Date">
                </div>
                <div class="col-md-1">
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        @foreach([5,10,25,50] as $pp)
                            <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom px-3">
                        <i class="bi bi-funnel"></i>
                    </button>
                    <a href="{{ url('/exception-history') }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- BULK ACTIONS --}}
    <form method="POST" action="{{ url('/exception-bulk-delete') }}" id="bulkForm">
        @csrf
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSelectAll()">
                    <i class="bi bi-check2-square me-1"></i>Select All
                </button>
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete selected?')">
                    <i class="bi bi-trash me-1"></i>Delete Selected
                </button>
                <form method="POST" action="{{ url('/exception-clear-old') }}" class="d-inline" onsubmit="return confirm('Delete logs older than 30 days?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning">
                        <i class="bi bi-clock-history me-1"></i>Clear Old (30d)
                    </button>
                </form>
                <form method="POST" action="{{ url('/exception-clear-all') }}" class="d-inline" onsubmit="return confirm('Clear ALL logs? This cannot be undone!')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash3 me-1"></i>Clear All
                    </button>
                </form>
            </div>
            <div>
                <a href="{{ url('/exception-export-csv') }}?{{ http_build_query(request()->all()) }}"
                   class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
                </a>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="table-card p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="selectAllChk" onchange="toggleSelectAll(this.checked)"></th>
                            <th>ID</th>
                            <th>Message</th>
                            <th>Type</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>IP</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="{{ $log->id }}" class="row-check"></td>
                            <td class="text-muted small">{{ $log->id }}</td>
                            <td style="max-width:200px">
                                <span class="d-inline-block text-truncate" style="max-width:180px" title="{{ $log->message }}">
                                    {{ $log->message }}
                                </span>
                            </td>
                            <td><span class="badge badge-pill badge-type">{{ $log->exception_type }}</span></td>
                            <td>
                                <span class="badge badge-pill badge-severity-{{ $log->severity }}">
                                    {{ ucfirst($log->severity) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-pill badge-status-{{ $log->status }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $log->ip_address ?? '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $log->http_method ?? '-' }}</span></td>
                            <td class="small text-muted">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    {{-- Detail Modal --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="showDetail({{ $log->id }}, {{ json_encode($log->message) }}, {{ json_encode($log->url) }}, {{ json_encode($log->exception_type) }}, {{ json_encode($log->severity) }}, {{ json_encode($log->status) }}, {{ json_encode($log->ip_address) }}, {{ json_encode($log->user_agent) }}, {{ json_encode($log->http_method) }}, {{ json_encode($log->stack_trace) }}, {{ json_encode($log->request_payload) }}, {{ json_encode($log->created_at->format('d M Y H:i:s')) }})">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Status Update --}}
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @foreach(['open','investigating','resolved'] as $st)
                                                <li>
                                                    <form method="POST" action="{{ url('/exception-status/'.$log->id) }}">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="{{ $st }}">
                                                        <button type="submit" class="dropdown-item {{ $log->status == $st ? 'fw-bold' : '' }}">
                                                            {{ ucfirst($st) }}
                                                        </button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ url('/exception-delete/'.$log->id) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="empty-box">
                                <i class="bi bi-inbox display-4 d-block mb-2"></i>No exception logs found
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <small class="text-muted">
            Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
        </small>
        <ul class="pagination mb-0">
            @for($i = 1; $i <= $logs->lastPage(); $i++)
                <li class="page-item {{ $logs->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link" href="{{ $logs->url($i) }}">{{ $i }}</a>
                </li>
            @endfor
        </ul>
    </div>

</div>

{{-- DETAIL MODAL --}}
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-bug me-2"></i>Exception Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Message</label>
                        <p id="d-message" class="fw-semibold mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">URL</label>
                        <p id="d-url" class="mb-0"><a id="d-url-link" href="#" target="_blank" class="text-primary"></a></p>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Type</label>
                        <p id="d-type" class="mb-0"></p>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Severity</label>
                        <p id="d-severity" class="mb-0"></p>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Status</label>
                        <p id="d-status" class="mb-0"></p>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Date</label>
                        <p id="d-date" class="mb-0 small"></p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">IP Address</label>
                        <p id="d-ip" class="mb-0"></p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">HTTP Method</label>
                        <p id="d-method" class="mb-0"></p>
                    </div>
                    <div class="col-md-12">
                        <label class="text-muted small">User Agent</label>
                        <p id="d-ua" class="mb-0 small text-muted"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Request Payload</label>
                    <pre id="d-payload" class="stack-trace"></pre>
                </div>
                <div>
                    <label class="text-muted small">Stack Trace</label>
                    <pre id="d-trace" class="stack-trace"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Dark Mode
    function toggleDark() {
        const html = document.getElementById('htmlRoot');
        const isDark = html.getAttribute('data-theme') === 'dark';
        html.setAttribute('data-theme', isDark ? '' : 'dark');
        document.getElementById('darkIcon').className = isDark ? 'bi bi-moon-stars-fill' : 'bi bi-sun-fill';
        localStorage.setItem('theme', isDark ? 'light' : 'dark');
    }
    if (localStorage.getItem('theme') === 'dark') {
        document.getElementById('htmlRoot').setAttribute('data-theme', 'dark');
        document.getElementById('darkIcon').className = 'bi bi-sun-fill';
    }

    // Select All
    function toggleSelectAll(checked) {
        document.querySelectorAll('.row-check').forEach(c => c.checked = checked ?? !c.checked);
        const chk = document.getElementById('selectAllChk');
        if (checked !== undefined) chk.checked = checked;
    }

    // Detail Modal
    function showDetail(id, msg, url, type, severity, status, ip, ua, method, trace, payload, date) {
        document.getElementById('d-message').textContent  = msg;
        document.getElementById('d-url-link').textContent = url;
        document.getElementById('d-url-link').href        = url;
        document.getElementById('d-type').textContent     = type;
        document.getElementById('d-severity').textContent = severity;
        document.getElementById('d-status').textContent   = status;
        document.getElementById('d-ip').textContent       = ip || '-';
        document.getElementById('d-ua').textContent       = ua || '-';
        document.getElementById('d-method').textContent   = method || '-';
        document.getElementById('d-date').textContent     = date;
        document.getElementById('d-trace').textContent    = trace || 'No stack trace available';
        document.getElementById('d-payload').textContent  = payload ? JSON.stringify(payload, null, 2) : '{}';
        new bootstrap.Modal(document.getElementById('detailModal')).show();
    }

    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'bar',
        data: {
            labels: {!! $chartData->pluck('date')->toJson() !!},
            datasets: [{
                label: 'Exceptions',
                data: {!! $chartData->pluck('count')->toJson() !!},
                backgroundColor: 'rgba(99,102,241,0.7)',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Severity Pie Chart
    const sevCtx = document.getElementById('severityChart').getContext('2d');
    new Chart(sevCtx, {
        type: 'doughnut',
        data: {
            labels: {!! collect(['low','medium','high','critical'])->toJson() !!},
            datasets: [{
                data: [
                    {{ $severityData['low'] ?? 0 }},
                    {{ $severityData['medium'] ?? 0 }},
                    {{ $severityData['high'] ?? 0 }},
                    {{ $severityData['critical'] ?? 0 }}
                ],
                backgroundColor: ['#22c55e','#f59e0b','#ef4444','#1e293b'],
                borderWidth: 2,
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
</script>
</body>
</html>
