<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Mail\SalesInquiryMail;
use App\Mail\UpdateClientMail;

class MailController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'to_email' => ['required','email'],
            'to_name' => ['nullable','string','max:255'],
            'from_email' => ['required','email'],
            'from_name' => ['nullable','string','max:255'],
            'subject' => ['required','string','max:255'],
            'content' => ['required','string'],
            'update_client' => ['sometimes','boolean'],
        ]);

        $updateClient = (bool)($validated['update_client'] ?? false);

        try {
            // Action A: send email to sales@avinertech.com
            Mail::to(['address' => 'sales@avinertech.com', 'name' => 'AvinerTech Sales'])
                ->send(new SalesInquiryMail(
                    toEmail: $validated['to_email'],
                    toName: $validated['to_name'] ?? null,
                    fromEmail: $validated['from_email'],
                    fromName: $validated['from_name'] ?? null,
                    subject: $validated['subject'],
                    content: $validated['content']
                ));

            // Action B: optional update to the original sender
            $actionB = false;
            if ($updateClient) {
                Mail::to(['address' => $validated['from_email'], 'name' => $validated['from_name'] ?? null])
                    ->send(new UpdateClientMail(
                        toEmail: $validated['to_email'],
                        toName: $validated['to_name'] ?? null,
                        subject: $validated['subject']
                    ));
                $actionB = true;
            }

            return response()->json([
                'success' => true,
                'message' => 'Email processed successfully',
                'action_b_performed' => $actionB,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process email',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}


