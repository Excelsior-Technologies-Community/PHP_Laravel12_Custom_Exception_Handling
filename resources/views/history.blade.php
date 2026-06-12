<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exception Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f6f8fc;
            font-family: 'Segoe UI', sans-serif;
        }

        /* HEADER */
        .dashboard-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 40px;
            border-radius: 22px;
            margin-bottom: 25px;
            box-shadow: 0 15px 40px rgba(79,70,229,.25);
        }

        .dashboard-header h2 {
            font-weight: 800;
        }

        /* STATS CARDS */
        .stats-card {
            background: white;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,.06);
            transition: .3s;
            border-left: 6px solid #7c3aed;
        }

        .stats-card:hover {
            transform: translateY(-6px);
        }

        .stats-number {
            font-size: 32px;
            font-weight: 800;
        }

        .total { color: #7c3aed; }
        .today { color: #06b6d4; }
        .latest { color: #22c55e; }

        /* SEARCH */
        .search-box {
            border-radius: 50px;
            padding: 14px 20px;
        }

        .search-btn {
            border-radius: 50px;
            background: #7c3aed;
            color: white;
            border: none;
            padding: 0 25px;
        }

        .search-btn:hover {
            background: #6d28d9;
        }

        /* TABLE */
        .table-card {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,.06);
        }

        .table thead {
            background: #111827;
            color: white;
        }

        .table tbody tr:hover {
            background: #f9fafb;
        }

        /* BADGE */
        .badge-custom {
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        /* URL */
        .url-text {
            color: #4f46e5;
            text-decoration: none;
        }

        .url-text:hover {
            text-decoration: underline;
        }

        /* DELETE */
        .delete-btn {
            background: #ef4444;
            border: none;
            color: white;
            border-radius: 8px;
        }

        .delete-btn:hover {
            background: #dc2626;
        }

        /* PAGINATION */
        .pagination .page-link {
            border-radius: 10px;
            margin: 0 4px;
            color: #4f46e5;
        }

        .pagination .active .page-link {
            background: #4f46e5;
            border: none;
            color: white;
        }

        /* EMPTY */
        .empty-box {
            text-align: center;
            padding: 50px;
            color: #6b7280;
        }
    </style>
</head>

<body>

<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- HEADER -->
    <div class="dashboard-header">
        <h2><i class="bi bi-shield-lock me-2"></i> Exception Monitoring Dashboard</h2>
        <p>Professional exception tracking system with analytics & logs</p>
    </div>

    <!-- STATS -->
    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <h6>Total Exceptions</h6>
                <div class="stats-number total">
                    {{ $totalExceptions }}
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <h6>Today's Exceptions</h6>
                <div class="stats-number today">
                    {{ $todayExceptions }}
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <h6>Latest Exception</h6>
                <div class="stats-number latest" style="font-size:18px;">
                    {{ $latestException->exception_type ?? 'N/A' }}
                </div>
            </div>
        </div>

    </div>

    <!-- SEARCH -->
    <form method="GET" action="{{ url('/exception-history') }}" class="mb-4">
        <div class="input-group">
            <input type="text"
                   name="search"
                   class="form-control search-box"
                   placeholder="Search logs..."
                   value="{{ request('search') }}">

            <button class="btn search-btn">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </form>

    <!-- TABLE -->
    <div class="table-card">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead>
                <tr>
                    <th>ID</th>
                    <th>Message</th>
                    <th>URL</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>

                @forelse($logs as $log)

                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->message }}</td>
                        <td>
                            <a class="url-text" href="{{ $log->url }}" target="_blank">
                                Open
                            </a>
                        </td>
                        <td>
                            <span class="badge-custom">
                                {{ $log->exception_type }}
                            </span>
                        </td>
                        <td>
                            {{ $log->created_at->format('d M Y h:i A') }}
                        </td>
                        <td>
                            <form action="{{ url('/exception-delete/'.$log->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm delete-btn"
                                        onclick="return confirm('Delete this log?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="empty-box">
                            No Logs Found
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <!-- PAGINATION -->
    <div class="d-flex justify-content-center mt-4">

        <ul class="pagination">

            @for($i = 1; $i <= $logs->lastPage(); $i++)

                <li class="page-item {{ $logs->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link" href="{{ $logs->url($i) }}">
                        {{ $i }}
                    </a>
                </li>

            @endfor

        </ul>

    </div>

</div>

</body>
</html>