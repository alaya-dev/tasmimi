# Guide de Test - Fonctionnalité Templates Clients

Ce guide décrit les étapes pour tester manuellement la nouvelle fonctionnalité des templates clients.

## Prérequis

1. **Migration de la base de données**
   ```bash
   php artisan migrate
   ```

2. **Créer des données de test**
   ```bash
   php artisan db:seed
   ```

3. **Créer un utilisateur client de test**
   ```bash
   php artisan tinker
   ```
   ```php
   User::create([
       'email' => 'client@test.com',
       'password' => bcrypt('password'),
       'role' => 'client',
       'email_verified_at' => now()
   ]);
   ```

## Tests Automatisés

### 1. Test d'intégration complet
```bash
php artisan test:client-templates --cleanup
```

### 2. Tests unitaires
```bash
php artisan test tests/Feature/ClientTemplateTest.php
```

## Tests Manuels de l'Interface

### 1. Test de Navigation

#### Étape 1.1 : Connexion Client
1. Aller à `/login`
2. Se connecter avec `client@test.com` / `password`
3. ✅ **Vérifier** : Redirection vers la page d'accueil client

#### Étape 1.2 : Menu "تصاميمي"
1. Cliquer sur l'avatar utilisateur (menu déroulant)
2. ✅ **Vérifier** : Le lien "تصاميمي" est visible
3. Cliquer sur "تصاميمي"
4. ✅ **Vérifier** : Redirection vers `/client/my-designs`

### 2. Test de la Page "Mes Designs"

#### Étape 2.1 : État Vide
1. Sur la page "تصاميمي"
2. ✅ **Vérifier** : Message "ليس لديك أي تصاميم بعد"
3. ✅ **Vérifier** : Bouton "اختيار قالب" présent
4. ✅ **Vérifier** : Interface 100% en arabe avec support RTL

#### Étape 2.2 : Navigation vers Templates
1. Cliquer sur "اختيار قالب" ou "إنشاء تصميم جديد"
2. ✅ **Vérifier** : Redirection vers la page des templates

### 3. Test de Création de Design

#### Étape 3.1 : Sélection de Template
1. Sur la page des templates, cliquer sur un template
2. ✅ **Vérifier** : Redirection vers l'éditeur
3. ✅ **Vérifier** : URL contient `/client/templates/{id}/create`

#### Étape 3.2 : Interface de l'Éditeur
1. Dans l'éditeur
2. ✅ **Vérifier** : Interface en arabe avec support RTL
3. ✅ **Vérifier** : Sidebar avec onglets "العناصر" et "الخصائص"
4. ✅ **Vérifier** : Canvas au centre
5. ✅ **Vérifier** : Filigrane "سامقة للتصميم" visible en bas à droite
6. ✅ **Vérifier** : Notice "يحتوي التصميم على علامة مائية محمية"

#### Étape 3.3 : Ajout d'Éléments
1. Cliquer sur l'onglet "العناصر"
2. Tester l'ajout de différents éléments :
   - Texte
   - Titre
   - Formes (carré, cercle)
   - Images
3. ✅ **Vérifier** : Éléments ajoutés correctement au canvas
4. ✅ **Vérifier** : Sélection d'élément fonctionne

#### Étape 3.4 : Filigrane Non-Supprimable
1. Essayer de sélectionner le filigrane
2. ✅ **Vérifier** : Le filigrane ne peut pas être sélectionné
3. ✅ **Vérifier** : Le filigrane reste toujours visible
4. ✅ **Vérifier** : Opacité du filigrane plus élevée que l'admin (0.6)

### 4. Test de Sauvegarde

#### Étape 4.1 : Première Sauvegarde
1. Cliquer sur "حفظ التصميم"
2. ✅ **Vérifier** : Modal de sauvegarde s'ouvre
3. Remplir le nom : "تصميم تجريبي"
4. Ajouter des notes (optionnel)
5. Cliquer sur "حفظ"
6. ✅ **Vérifier** : Message de succès
7. ✅ **Vérifier** : Redirection ou mise à jour de l'URL

#### Étape 4.2 : Sauvegarde Subséquente
1. Modifier le design
2. Cliquer sur "حفظ التصميم"
3. ✅ **Vérifier** : Sauvegarde directe sans modal
4. ✅ **Vérifier** : Version incrémentée

### 5. Test de la Liste des Designs

#### Étape 5.1 : Retour à "Mes Designs"
1. Cliquer sur "العودة" ou naviguer vers "تصاميمي"
2. ✅ **Vérifier** : Le design créé apparaît dans la liste
3. ✅ **Vérifier** : Informations correctes (nom, catégorie, version, date)

#### Étape 5.2 : Actions sur les Designs
1. Hover sur une carte de design
2. ✅ **Vérifier** : Overlay avec boutons d'action apparaît
3. Tester les boutons :
   - ✅ **Éditer** : Ouvre l'éditeur
   - ✅ **Dupliquer** : Crée une copie
   - ✅ **Exporter** : Ouvre le modal d'export

### 6. Test d'Édition

#### Étape 6.1 : Ouverture en Mode Édition
1. Cliquer sur "تحرير" sur un design existant
2. ✅ **Vérifier** : Éditeur s'ouvre avec le design chargé
3. ✅ **Vérifier** : URL contient `/client/designs/{id}/edit`
4. ✅ **Vérifier** : Filigrane toujours présent et non-modifiable

#### Étape 6.2 : Modifications
1. Modifier des éléments existants
2. Ajouter de nouveaux éléments
3. Sauvegarder
4. ✅ **Vérifier** : Modifications sauvegardées
5. ✅ **Vérifier** : Version incrémentée

### 7. Test d'Export

#### Étape 7.1 : Modal d'Export
1. Cliquer sur "تصدير" dans l'éditeur ou sur une carte
2. ✅ **Vérifier** : Modal d'export s'ouvre
3. ✅ **Vérifier** : Options de format (PNG, JPEG, SVG, PDF)
4. ✅ **Vérifier** : Contrôle de qualité pour formats supportés
5. ✅ **Vérifier** : Notice sur l'inclusion du filigrane

#### Étape 7.2 : Export PNG
1. Sélectionner format PNG
2. Cliquer sur "تصدير"
3. ✅ **Vérifier** : Téléchargement automatique
4. ✅ **Vérifier** : Fichier contient le design avec filigrane
5. ✅ **Vérifier** : Nom de fichier correct avec timestamp

#### Étape 7.3 : Export Autres Formats
1. Tester JPEG avec différentes qualités
2. Tester SVG
3. Tester PDF (si implémenté)
4. ✅ **Vérifier** : Tous les exports contiennent le filigrane

### 8. Test de Duplication

#### Étape 8.1 : Duplication
1. Cliquer sur "نسخ" sur un design
2. ✅ **Vérifier** : Nouveau design créé avec " - نسخة" ajouté au nom
3. ✅ **Vérifier** : Version remise à 1
4. ✅ **Vérifier** : Design dupliqué apparaît dans la liste

### 9. Test de Suppression

#### Étape 9.1 : Modal de Confirmation
1. Cliquer sur le bouton de suppression (icône poubelle)
2. ✅ **Vérifier** : Modal de confirmation s'ouvre
3. ✅ **Vérifier** : Message en arabe avec nom du design

#### Étape 9.2 : Suppression
1. Confirmer la suppression
2. ✅ **Vérifier** : Design supprimé de la liste
3. ✅ **Vérifier** : Message de succès

### 10. Tests de Sécurité

#### Étape 10.1 : Isolation des Données
1. Créer un second compte client
2. ✅ **Vérifier** : Chaque client ne voit que ses propres designs
3. ✅ **Vérifier** : Impossible d'accéder aux designs d'autres clients

#### Étape 10.2 : Restrictions Admin
1. Se connecter en tant qu'admin
2. Essayer d'accéder à `/client/my-designs`
3. ✅ **Vérifier** : Redirection vers le dashboard admin

#### Étape 10.3 : Filigrane Obligatoire
1. Inspecter les données sauvegardées
2. ✅ **Vérifier** : Filigrane toujours présent dans les données JSON
3. ✅ **Vérifier** : Propriété `removable: false`

## Tests de Performance

### 1. Chargement des Pages
- ✅ Page "Mes Designs" se charge en < 2 secondes
- ✅ Éditeur se charge en < 3 secondes
- ✅ Export se termine en < 10 secondes

### 2. Réactivité
- ✅ Interface responsive sur mobile/tablette
- ✅ Support RTL correct sur tous les écrans

## Résolution des Problèmes

### Problèmes Courants

1. **Migration échoue**
   ```bash
   php artisan migrate:rollback
   php artisan migrate
   ```

2. **Routes non trouvées**
   ```bash
   php artisan route:clear
   php artisan route:cache
   ```

3. **Erreurs JavaScript**
   ```bash
   npm run build
   ```

4. **Problèmes de permissions**
   ```bash
   php artisan storage:link
   chmod -R 755 storage/
   ```

## Critères de Validation

✅ **Fonctionnalité complète** : Tous les tests passent
✅ **Interface en arabe** : 100% des textes en arabe
✅ **Support RTL** : Layout correct de droite à gauche
✅ **Filigrane obligatoire** : Toujours présent et non-supprimable
✅ **Sécurité** : Isolation des données clients
✅ **Performance** : Temps de réponse acceptables
✅ **Export multi-format** : Tous les formats fonctionnent
✅ **Responsive** : Fonctionne sur tous les écrans
