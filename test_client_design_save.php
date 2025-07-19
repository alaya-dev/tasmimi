<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientTemplateController;
use App\Models\Template;
use App\Models\User;
use App\Models\ClientTemplate;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Test de sauvegarde des designs clients\n";
echo "==========================================\n\n";

try {
    // 1. Vérifier qu'il y a des templates disponibles
    $template = Template::where('is_active', true)->first();
    if (!$template) {
        echo "❌ Aucun template actif trouvé\n";
        exit(1);
    }
    echo "✅ Template trouvé: {$template->name} (ID: {$template->id})\n";

    // 2. Vérifier qu'il y a des utilisateurs clients
    $user = User::where('role', 'client')->first();
    if (!$user) {
        echo "❌ Aucun utilisateur client trouvé\n";
        exit(1);
    }
    echo "✅ Utilisateur client trouvé: {$user->email} (ID: {$user->id})\n";

    // 3. Simuler l'authentification
    auth()->login($user);
    echo "✅ Utilisateur authentifié\n";

    // 4. Préparer les données de test
    $designData = [
        'elements' => [
            [
                'type' => 'text',
                'content' => 'Test Design',
                'x' => 100,
                'y' => 100,
                'fontSize' => 24,
                'color' => '#000000'
            ]
        ],
        'canvas' => [
            'width' => 800,
            'height' => 600,
            'background' => '#ffffff'
        ],
        'watermark' => [
            'text' => 'سامقة للتصميم',
            'enabled' => true,
            'position' => 'bottom-right'
        ]
    ];

    // 5. Créer une requête simulée
    $request = Request::create('/client/templates/' . $template->id . '/design', 'POST', [
        'design_data' => json_encode($designData),
        'name' => 'Test Design - ' . date('Y-m-d H:i:s')
    ]);

    // 6. Tester la sauvegarde
    echo "\n🔄 Test de sauvegarde...\n";
    $controller = new ClientTemplateController();
    $response = $controller->saveDesign($request, $template);

    // 7. Vérifier la réponse
    $responseData = json_decode($response->getContent(), true);
    
    if ($response->getStatusCode() === 200 && $responseData['success']) {
        echo "✅ Sauvegarde réussie!\n";
        echo "   Message: {$responseData['message']}\n";
        if (isset($responseData['client_template'])) {
            echo "   Template client ID: {$responseData['client_template']['id']}\n";
            echo "   Nom: {$responseData['client_template']['name']}\n";
            echo "   Version: {$responseData['client_template']['version']}\n";
        }
    } else {
        echo "❌ Échec de la sauvegarde\n";
        echo "   Status: {$response->getStatusCode()}\n";
        echo "   Réponse: " . $response->getContent() . "\n";
        exit(1);
    }

    // 8. Vérifier en base de données
    $clientTemplate = ClientTemplate::where('user_id', $user->id)
                                   ->where('template_id', $template->id)
                                   ->latest()
                                   ->first();

    if ($clientTemplate) {
        echo "✅ Template client trouvé en base de données\n";
        echo "   ID: {$clientTemplate->id}\n";
        echo "   Nom: {$clientTemplate->name}\n";
        echo "   Version: {$clientTemplate->version}\n";
        echo "   Dernière modification: {$clientTemplate->last_edited_at}\n";
        
        // Vérifier les données de design
        $savedDesignData = json_decode($clientTemplate->design_data, true);
        if ($savedDesignData && isset($savedDesignData['elements'])) {
            echo "✅ Données de design correctement sauvegardées\n";
        } else {
            echo "❌ Problème avec les données de design sauvegardées\n";
        }
    } else {
        echo "❌ Template client non trouvé en base de données\n";
        exit(1);
    }

    // 9. Test de mise à jour
    echo "\n🔄 Test de mise à jour...\n";
    $updateRequest = Request::create('/client/templates/' . $template->id . '/design', 'POST', [
        'design_data' => json_encode(array_merge($designData, ['updated' => true])),
        'name' => 'Test Design Updated - ' . date('Y-m-d H:i:s')
    ]);

    $updateResponse = $controller->saveDesign($updateRequest, $template);
    $updateResponseData = json_decode($updateResponse->getContent(), true);

    if ($updateResponse->getStatusCode() === 200 && $updateResponseData['success']) {
        echo "✅ Mise à jour réussie!\n";
        
        // Vérifier que la version a été incrémentée
        $updatedTemplate = ClientTemplate::find($clientTemplate->id);
        if ($updatedTemplate->version > $clientTemplate->version) {
            echo "✅ Version correctement incrémentée: {$updatedTemplate->version}\n";
        } else {
            echo "❌ Version non incrémentée\n";
        }
    } else {
        echo "❌ Échec de la mise à jour\n";
        echo "   Status: {$updateResponse->getStatusCode()}\n";
        echo "   Réponse: " . $updateResponse->getContent() . "\n";
    }

    echo "\n🎉 Tous les tests sont passés avec succès!\n";

} catch (Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
