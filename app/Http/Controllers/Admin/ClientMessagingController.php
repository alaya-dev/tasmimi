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
            'client_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ], [
            'client_id.required' => 'يجب اختيار العميل.',
            'client_id.exists' => 'العميل المحدد غير موجود.',
            'subject.required' => 'يجب إدخال موضوع الرسالة.',
            'subject.max' => 'موضوع الرسالة لا يجب أن يتجاوز 255 حرف.',
            'message.required' => 'يجب إدخال محتوى الرسالة.',
            'message.max' => 'محتوى الرسالة لا يجب أن يتجاوز 2000 حرف.',
        ]);

        try {
            $client = User::findOrFail($request->client_id);
            $currentUser = Auth::user();
            
            // Determine sender name based on role
            $senderName = $currentUser->role === 'super_admin' ? 
                'المدير العام - فريق سامقة للتصميم' : 
                'الإدارة - فريق سامقة للتصميم';

            // Send the notification
            $client->notify(new CustomClientMessage(
                $request->subject,
                $request->message,
                $senderName
            ));

            return redirect()->back()->with('success', 'تم إرسال الرسالة بنجاح إلى ' . $client->email);

        } catch (\Exception $e) {
            \Log::error('Failed to send client message: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.');
        }
    }
}
