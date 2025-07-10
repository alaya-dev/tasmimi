<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Configuration de la base de données
$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'bitaqati',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    // Vérifier si la table existe
    if (!Capsule::schema()->hasTable('subscriptions')) {
        echo "Création de la table subscriptions...\n";
        
        Capsule::schema()->create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        echo "Table subscriptions créée avec succès!\n";
        
        // Insérer des données de test
        echo "Insertion des données de test...\n";
        
        $subscriptions = [
            [
                'name' => 'اشتراك أسبوعي',
                'type' => 'weekly',
                'price' => 29.99,
                'description' => 'اشتراك أسبوعي للوصول إلى جميع القوالب والميزات',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'اشتراك شهري',
                'type' => 'monthly',
                'price' => 99.99,
                'description' => 'اشتراك شهري للوصول إلى جميع القوالب والميزات مع خصم 15%',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'اشتراك سنوي',
                'type' => 'annual',
                'price' => 999.99,
                'description' => 'اشتراك سنوي للوصول إلى جميع القوالب والميزات مع خصم 30%',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        foreach ($subscriptions as $subscription) {
            Capsule::table('subscriptions')->insert($subscription);
        }
        
        echo "Données de test insérées avec succès!\n";
    } else {
        echo "La table subscriptions existe déjà.\n";
        
        // Vérifier s'il y a des données
        $count = Capsule::table('subscriptions')->count();
        echo "Nombre d'abonnements existants: $count\n";
        
        if ($count === 0) {
            echo "Insertion des données de test...\n";
            
            $subscriptions = [
                [
                    'name' => 'اشتراك أسبوعي',
                    'type' => 'weekly',
                    'price' => 29.99,
                    'description' => 'اشتراك أسبوعي للوصول إلى جميع القوالب والميزات',
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'اشتراك شهري',
                    'type' => 'monthly',
                    'price' => 99.99,
                    'description' => 'اشتراك شهري للوصول إلى جميع القوالب والميزات مع خصم 15%',
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'اشتراك سنوي',
                    'type' => 'annual',
                    'price' => 999.99,
                    'description' => 'اشتراك سنوي للوصول إلى جميع القوالب والميزات مع خصم 30%',
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ];
            
            foreach ($subscriptions as $subscription) {
                Capsule::table('subscriptions')->insert($subscription);
            }
            
            echo "Données de test insérées avec succès!\n";
        }
    }
    
    echo "Configuration terminée!\n";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
