<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $columns = DB::select('DESCRIBE terms_of_service');
    
    echo "Structure de la table terms_of_service :\n";
    echo str_repeat("=", 80) . "\n";
    printf("%-20s %-15s %-8s %-8s %-8s %-10s\n", 'Field', 'Type', 'Null', 'Key', 'Default', 'Extra');
    echo str_repeat("-", 80) . "\n";
    
    foreach ($columns as $column) {
        printf("%-20s %-15s %-8s %-8s %-8s %-10s\n", 
            $column->Field, 
            $column->Type, 
            $column->Null, 
            $column->Key, 
            $column->Default ?? 'NULL', 
            $column->Extra
        );
    }
    
    echo str_repeat("=", 80) . "\n";
    
    // Check specifically for extracted_content column
    $extractedContentColumn = collect($columns)->where('Field', 'extracted_content')->first();
    if ($extractedContentColumn) {
        echo "\nColonne extracted_content :\n";
        echo "Type: " . $extractedContentColumn->Type . "\n";
        echo "Null: " . $extractedContentColumn->Null . "\n";
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
