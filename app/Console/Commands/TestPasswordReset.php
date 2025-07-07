<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;

class TestPasswordReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password-reset {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test password reset email functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('Testing password reset for: ' . $email);
        
        // Check if user exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error('User not found with email: ' . $email);
            return Command::FAILURE;
        }
        
        $this->info('User found: ' . $user->email);
        
        // Test sending password reset
        try {
            $status = Password::sendResetLink(['email' => $email]);
            
            if ($status === Password::RESET_LINK_SENT) {
                $this->info('✅ Password reset email sent successfully!');
                $this->info('Check the email inbox for: ' . $email);
                return Command::SUCCESS;
            } else {
                $this->error('❌ Failed to send password reset email. Status: ' . $status);
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error sending email: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}