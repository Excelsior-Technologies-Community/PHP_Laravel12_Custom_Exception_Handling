<!DOCTYPE html>
<html>

<head>

    <title>Exception History</title>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body style="background:#f5f7fb;">

    <div class="container py-5">

        <div class="card shadow-lg border-0">

            <div class="card-header bg-danger text-white">

                <h2 class="mb-0">
                    Exception History Dashboard
                </h2>

            </div>

            <div class="card-body">

                <!-- Total Logs -->
                <div class="mb-4">

                    <div class="alert alert-primary">

                        <strong>Total Exceptions:</strong>

                        {{ $logs->count() }}

                    </div>

                </div>

                <!-- Table -->
                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-dark">

                            <tr>

                                <th>ID</th>

                                <th>Message</th>

                                <th>URL</th>

                                <th>Exception Type</th>

                                <th>Created At</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($logs as $log)

                                <tr>

                                    <td>
                                        {{ $log->id }}
                                    </td>

                                    <td>
                                        {{ $log->message }}
                                    </td>

                                    <td>

                                        <span class="text-primary">

                                            {{ $log->url }}

                                        </span>

                                    </td>

                                    <td>

                                        <span class="badge bg-danger">

                                            {{ $log->exception_type }}

                                        </span>

                                    </td>

                                    <td>

                                        {{ $log->created_at->format('d M Y h:i A') }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="text-center text-muted">

                                        No Exception Logs Found

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</body>

</html>