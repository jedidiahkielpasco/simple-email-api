<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Logs Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .subject-cell {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .body-cell {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .message-id-cell {
            font-family: 'Courier New', monospace;
            font-size: 0.85em;
        }
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
                        <th>Subject</th>
                        <th>Body Preview</th>
                        <th>Message ID</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emailLogs as $log)
                        <tr>
                            <td><code>{{ $log->id }}</code></td>
                            <td>
                                <a href="mailto:{{ $log->to }}" class="text-decoration-none">
                                    {{ $log->to }}
                                </a>
                            </td>
                            <td>
                                <a href="mailto:{{ $log->from }}" class="text-decoration-none">
                                    {{ $log->from }}
                                </a>
                            </td>
                            <td class="subject-cell" title="{{ $log->subject }}">
                                {{ $log->subject ?? '-' }}
                            </td>
                            <td class="body-cell" title="{{ $log->body }}">
                                {{ $log->body ? Str::limit(strip_tags($log->body), 50) : '-' }}
                            </td>
                            <td class="message-id-cell">
                                @if($log->message_id)
                                    <code class="small">{{ $log->message_id }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $log->created_at->format('M j, Y') }}<br>
                                    {{ $log->created_at->format('g:i A') }}
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <p class="text-muted">No email logs found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
