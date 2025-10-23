<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function table(Request $request)
    {
        $emailLogs = EmailLog::orderBy('created_at', 'desc')->get();

        return view('email_logs.table', compact('emailLogs'));
    }
}
