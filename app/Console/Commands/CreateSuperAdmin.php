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
                            {--name= : Le nom du Super Admin}
                            {--email= : L\'email du Super Admin}
                            {--password= : Le mot de passe du Super Admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer un compte Super Administrateur pour Bitaqati';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Création d\'un Super Administrateur ===');
        $this->newLine();

        // Récupérer les données depuis les options ou demander à l'utilisateur
        $name = $this->option('name') ?: $this->ask('Nom du Super Admin');
        $email = $this->option('email') ?: $this->ask('Email du Super Admin');
        $password = $this->option('password') ?: $this->secret('Mot de passe du Super Admin');

        // Valider les données
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('Erreurs de validation :');
            foreach ($validator->errors()->all() as $error) {
                $this->error('- ' . $error);
            }
            return Command::FAILURE;
        }

        // Vérifier s'il existe déjà un Super Admin
        $existingSuperAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
        if ($existingSuperAdmin) {
            $this->warn('Un Super Admin existe déjà :');
            $this->line('Nom: ' . $existingSuperAdmin->name);
            $this->line('Email: ' . $existingSuperAdmin->email);
            $this->newLine();
            
            if (!$this->confirm('Voulez-vous créer un autre Super Admin ?')) {
                $this->info('Opération annulée.');
                return Command::SUCCESS;
            }
        }

        // Créer le Super Admin
        try {
            $superAdmin = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => User::ROLE_SUPER_ADMIN,
                'email_verified_at' => now(),
            ]);

            $this->newLine();
            $this->info('✅ Super Admin créé avec succès !');
            $this->newLine();
            $this->line('Détails du compte :');
            $this->line('ID: ' . $superAdmin->id);
            $this->line('Nom: ' . $superAdmin->name);
            $this->line('Email: ' . $superAdmin->email);
            $this->line('Rôle: ' . $superAdmin->role);
            $this->line('Créé le: ' . $superAdmin->created_at->format('d/m/Y H:i:s'));
            $this->newLine();
            $this->info('Le Super Admin peut maintenant se connecter à l\'interface d\'administration.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erreur lors de la création du Super Admin :');
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
