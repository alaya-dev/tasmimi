<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitaqati:create-super-admin
                            {--email= : Super Admin email}
                            {--password= : Super Admin password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Super Administrator account for Bitaqati';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Creating Super Administrator ===');
        $this->newLine();

        // Get data from options or ask user
        $email = $this->option('email') ?: $this->ask('Super Admin Email');
        $password = $this->option('password');

        if (!$password) {
            $password = $this->secret('Super Admin Password');
            $passwordConfirmation = $this->secret('Confirm Password');

            // Validate password confirmation
            if ($password !== $passwordConfirmation) {
                $this->error('Password confirmation does not match.');
                return Command::FAILURE;
            }
        }

        // Validate data
        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
        ], [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        if ($validator->fails()) {
            $this->error('Validation errors:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('- ' . $error);
            }
            return Command::FAILURE;
        }

        // Check if Super Admin already exists
        $existingSuperAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
        if ($existingSuperAdmin) {
            $this->warn('A Super Admin already exists:');
            $this->line('Email: ' . $existingSuperAdmin->email);
            $this->newLine();

            if (!$this->confirm('Do you want to create another Super Admin?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        // Create Super Admin
        try {
            $superAdmin = User::create([
                'email' => $email,
                'password' => Hash::make($password),
                'role' => User::ROLE_SUPER_ADMIN,
                'email_verified_at' => now(),
            ]);

            $this->newLine();
            $this->info('✅ Super Admin created successfully!');
            $this->newLine();
            $this->line('Account details:');
            $this->line('ID: ' . $superAdmin->id);
            $this->line('Email: ' . $superAdmin->email);
            $this->line('Role: ' . $superAdmin->role);
            $this->line('Created at: ' . $superAdmin->created_at->format('Y-m-d H:i:s'));
            $this->newLine();
            $this->info('The Super Admin can now log in to the administration interface.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error creating Super Admin:');
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
