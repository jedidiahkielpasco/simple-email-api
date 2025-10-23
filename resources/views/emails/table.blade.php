<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-badge {
            font-size: 0.8em;
            padding: 0.25em 0.5em;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-processing { background-color: #17a2b8; color: #fff; }
        .status-sent { background-color: #28a745; color: #fff; }
        .status-failed { background-color: #dc3545; color: #fff; }
        
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>To</th>
                        <th>From</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Sent At</th>
                        <th>Failed At</th>
                        <th>Message ID</th>
                        <th>Error Message</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emails as $email)
                        <tr>
                            <td><code>{{ $email->id }}</code></td>
                            <td>
                                <a href="mailto:{{ $email->to }}" class="text-decoration-none">
                                    {{ $email->to }}
                                </a>
                            </td>
                            <td>
                                <a href="mailto:{{ $email->from }}" class="text-decoration-none">
                                    {{ $email->from }}
                                </a>
                            </td>
                            <td>
                                <span class="badge status-badge status-{{ $email->status }}">
                                    {{ ucfirst($email->status) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $email->created_at->format('M j, Y') }}<br>
                                    {{ $email->created_at->format('g:i A') }}
                                </small>
                            </td>
                            <td>
                                @if($email->sent_at)
                                    <small class="text-success">
                                        {{ $email->sent_at->format('M j, Y g:i A') }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($email->failed_at)
                                    <small class="text-danger">
                                        {{ $email->failed_at->format('M j, Y g:i A') }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($email->message_id)
                                    <code class="small">{{ $email->message_id }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($email->error_message)
                                    <span class="text-danger small" title="{{ $email->error_message }}">
                                        {{ Str::limit($email->error_message, 30) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <p class="text-muted">No emails found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
