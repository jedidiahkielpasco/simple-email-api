<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailRequest;
use App\Jobs\ProcessEmail;
use App\Models\Email;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function store(StoreEmailRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            $subject = $validated['subject'];
            $body = $validated['body'];
            
            unset($validated['subject'], $validated['body']);
            
            $email = Email::create($validated);

            ProcessEmail::dispatch($email, $subject, $body);

            return response()->json([
                'success' => true,
                'message' => 'Email queued for sending',
                'data' => [
                    'id' => $email->id,
                    'status' => 'pending',
                    'created_at' => $email->created_at,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create email',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Email::count(),
            'pending' => Email::where('status', Email::STATUS_PENDING)->count(),
            'processing' => Email::where('status', Email::STATUS_PROCESSING)->count(),
            'sent' => Email::where('status', Email::STATUS_SENT)->count(),
            'failed' => Email::where('status', Email::STATUS_FAILED)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function table(Request $request)
    {
        $emails = Email::orderBy('created_at', 'desc')->get();

        return view('emails.table', compact('emails'));
    }
}
