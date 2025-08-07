<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CustomClientMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientMessagingController extends Controller
{
    /**
     * Display the client messaging form.
     */
    public function index()
    {
        // Get only clients (users who don't have admin privileges)
        $clients = User::whereNotIn('role', ['admin', 'super_admin'])
            ->select('id', 'email', 'phone')
            ->orderBy('email')
            ->get();

        return Inertia::render('Admin/ClientMessaging/Index', [
            'clients' => $clients
        ]);
    }

    /**
     * Send custom message to selected client.
     */
    public function send(Request $request)
    {
        $request->validate([
            'send_type' => 'required|in:single,all',
            'client_id' => 'required_if:send_type,single|nullable|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ], [
            'send_type.required' => 'يجب اختيار نوع الإرسال.',
            'send_type.in' => 'نوع الإرسال غير صحيح.',
            'client_id.required_if' => 'يجب اختيار العميل.',
            'client_id.exists' => 'العميل المحدد غير موجود.',
            'subject.required' => 'يجب إدخال موضوع الرسالة.',
            'subject.max' => 'موضوع الرسالة لا يجب أن يتجاوز 255 حرف.',
            'message.required' => 'يجب إدخال محتوى الرسالة.',
            'message.max' => 'محتوى الرسالة لا يجب أن يتجاوز 2000 حرف.',
        ]);

        try {
            $currentUser = Auth::user();
            
            // Determine sender name based on role
            $senderName = $currentUser->role === 'super_admin' ? 
                'المدير العام - فريق سامقة للتصميم' : 
                'الإدارة - فريق سامقة للتصميم';

            if ($request->send_type === 'single') {
                // Send to single client
                $client = User::findOrFail($request->client_id);
                
                $client->notify(new CustomClientMessage(
                    $request->subject,
                    $request->message,
                    $senderName
                ));

                return redirect()->back()->with('success', 'تم إرسال الرسالة بنجاح إلى ' . $client->email);
                
            } else {
                // Send to all clients
                $clients = User::whereNotIn('role', ['admin', 'super_admin'])->get();
                
                if ($clients->count() === 0) {
                    return redirect()->back()->with('error', 'لا يوجد عملاء لإرسال الرسالة إليهم.');
                }

                $successCount = 0;
                $failedEmails = [];

                foreach ($clients as $client) {
                    try {
                        $client->notify(new CustomClientMessage(
                            $request->subject,
                            $request->message,
                            $senderName
                        ));
                        $successCount++;
                    } catch (\Exception $e) {
                        $failedEmails[] = $client->email;
                        \Log::error('Failed to send message to client: ' . $client->email . ' - Error: ' . $e->getMessage());
                    }
                }

                if ($successCount === $clients->count()) {
                    return redirect()->back()->with('success', 'تم إرسال الرسالة بنجاح إلى جميع العملاء (' . $successCount . ' عميل)');
                } else {
                    $message = 'تم إرسال الرسالة إلى ' . $successCount . ' من أصل ' . $clients->count() . ' عميل';
                    if (!empty($failedEmails)) {
                        $message .= '. فشل الإرسال إلى: ' . implode(', ', $failedEmails);
                    }
                    return redirect()->back()->with('error', $message);
                }
            }

        } catch (\Exception $e) {
            \Log::error('Failed to send client message: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.');
        }
    }
}
