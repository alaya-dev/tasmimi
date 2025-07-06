# Configuration Multilingue (Anglais/Arabe)

## Fonctionnalités implémentées

### 1. Structure des fichiers de langue
- `resources/lang/en/` - Traductions anglaises
- `resources/lang/ar/` - Traductions arabes
- Fichiers créés : `auth.php` et `common.php` pour chaque langue

### 2. Middleware SetLocale
- Gère automatiquement le changement de langue
- Stocke la langue sélectionnée en session
- Supporte les langues : `en` (anglais) et `ar` (arabe)

### 3. Contrôleur LanguageController
- Route POST `/language/{locale}` pour changer la langue
- Route GET `/language/current` pour obtenir la langue actuelle

### 4. Composant Vue LanguageSwitcher
- Sélecteur de langue avec drapeaux
- Interface utilisateur intuitive
- Support RTL pour l'arabe

### 5. Composable useTranslations
- Fonction `__()` pour les traductions côté client
- Détection automatique de la direction RTL
- Gestion des remplacements de variables

### 6. Support RTL (Right-to-Left)
- CSS adapté pour l'arabe
- Direction automatique basée sur la langue
- Polices optimisées pour l'arabe

### 7. Intégration Inertia.js
- TranslationServiceProvider pour partager les traductions
- Traductions disponibles dans toutes les pages Vue

## Utilisation

### Dans les templates Vue :
```vue
<template>
    <div :dir="direction">
        <h1>{{ __('common.welcome') }}</h1>
        <p>{{ __('auth.login') }}</p>
    </div>
</template>

<script setup>
import { useTranslations } from '@/Composables/useTranslations';
const { __, direction, isRTL } = useTranslations();
</script>
```

### Dans les contrôleurs Laravel :
```php
return __('auth.login');
// ou
return trans('common.welcome');
```

## Pages mises à jour
- ✅ Login.vue - Formulaire de connexion multilingue
- ✅ Dashboard.vue - Tableau de bord multilingue
- ✅ AuthenticatedLayout.vue - Navigation multilingue

## Langues supportées
- 🇺🇸 Anglais (en) - Par défaut
- 🇸🇦 Arabe (ar) - Support RTL complet

## Configuration
La langue par défaut est définie dans `config/app.php` :
```php
'locale' => 'en',
'fallback_locale' => 'en',
```

## Test
1. Accédez à la page de connexion
2. Utilisez le sélecteur de langue en haut à droite
3. Observez le changement de langue et de direction (RTL pour l'arabe)
4. La langue sélectionnée est persistée en session