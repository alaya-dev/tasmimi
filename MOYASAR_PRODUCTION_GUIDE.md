# 🚀 Guide de Migration Moyasar : Test → Production

## 📋 Vue d'ensemble

Ce guide explique comment passer du mode test au mode production pour les paiements Moyasar sans modifier le code.

---

## � Différences Test vs Production

### Mode Test (Actuel)
- ✅ **Clés API** : `sk_test_xxx` et `pk_test_xxx`
- ✅ **Cartes** : Fictives (4111111111111111, 4242424242424242)
- ✅ **Argent** : Aucun débit réel
- ✅ **Sécurité** : SSL optionnel

### Mode Production (Client Réel)
- 🔥 **Clés API** : `sk_live_xxx` et `pk_live_xxx`
- 🔥 **Cartes** : Vraies cartes bancaires
- 🔥 **Argent** : Vrais débits d'argent
- 🔥 **Sécurité** : SSL obligatoire

---

## 🛠️ Étapes de Migration

### **Étape 1 : Obtenir les Clés de Production**

Le client doit fournir :
```
Secret Key (Live) : sk_live_xxxxxxxxxxxxxxxxxx
Publishable Key (Live) : pk_live_xxxxxxxxxxxxxxxxxx
```

### **Étape 2 : Mettre à Jour le Fichier .env**

```env
# AVANT (Test)
MOYASAR_SECRET_KEY=sk_test_xxxxxxxxxx
MOYASAR_PUBLISHABLE_KEY=pk_test_xxxxxxxxxx
APP_ENV=local

# APRÈS (Production)
MOYASAR_SECRET_KEY=sk_live_xxxxxxxxxx
MOYASAR_PUBLISHABLE_KEY=pk_live_xxxxxxxxxx
APP_ENV=production
```

### **Étape 3 : Activer SSL en Production**

Modifier `app/Services/MoyasarService.php` :

```php
// Ligne ~75, changer :
'verify' => false,  // ❌ Mode test

// Vers :
'verify' => env('APP_ENV') === 'production',  // ✅ Mode production
```

### **Étape 4 : Configurer les Webhooks**

Dans le dashboard Moyasar du client :
```
URL de Webhook : https://votre-domaine.com/client/payment/callback
Événements : payment.paid, payment.failed
```

---

## 🧪 Tests de Validation

### **Test 1 : Paiement Réel**
1. Utiliser une vraie carte bancaire
2. Effectuer un paiement de 1 SAR
3. Vérifier le débit sur le compte
4. Confirmer l'activation de l'abonnement

### **Test 2 : Webhooks**
1. Effectuer un paiement
2. Vérifier les logs Laravel : `storage/logs/laravel.log`
3. Confirmer la réception du webhook

### **Test 3 : Erreurs**
1. Tester avec une carte expirée
2. Vérifier l'affichage des erreurs en arabe
3. Confirmer que l'abonnement n'est pas activé

---

## 🔒 Sécurité en Production

### **SSL/HTTPS Obligatoire**
```bash
# Vérifier que le site utilise HTTPS
curl -I https://votre-domaine.com
```

### **Variables d'Environnement Sécurisées**
```env
# Ne jamais commiter ces clés dans Git !
MOYASAR_SECRET_KEY=sk_live_xxx  # ⚠️ CONFIDENTIEL
MOYASAR_PUBLISHABLE_KEY=pk_live_xxx
```

### **Logs de Production**
```php
// Vérifier les logs de paiement
tail -f storage/logs/laravel.log | grep Moyasar
```

---

## 📊 Checklist de Migration

### **Avant la Migration**
- [ ] Obtenir les clés de production du client
- [ ] Sauvegarder la base de données
- [ ] Tester en mode test une dernière fois
- [ ] Préparer un plan de rollback

### **Pendant la Migration**
- [ ] Mettre à jour `.env` avec les clés live
- [ ] Activer SSL dans le code
- [ ] Configurer les webhooks
- [ ] Redémarrer les services (queue, cache)

### **Après la Migration**
- [ ] Tester un paiement réel de 1 SAR
- [ ] Vérifier les webhooks
- [ ] Monitorer les logs pendant 24h
- [ ] Confirmer avec le client

---

## 🚨 Points d'Attention

### **⚠️ Aucun Changement de Code Requis**
- Les URLs API restent identiques
- La logique de paiement ne change pas
- Seules les clés et la configuration changent

### **⚠️ Cartes de Test vs Réelles**
```javascript
// Mode Test (ne fonctionne plus en prod)
card_number: "4111111111111111"  // ❌ Carte fictive

// Mode Production (obligatoire)
card_number: "5123456789012346"  // ✅ Vraie carte client
```

### **⚠️ Montants Réels**
```php
// En production, tous les montants sont débités !
'amount' => 10000,  // = 100.00 SAR réels
```

---

## 🔧 Dépannage

### **Erreur : "Invalid API Key"**
```bash
# Vérifier les clés dans .env
php artisan config:clear
php artisan cache:clear
```

### **Erreur SSL**
```php
// Temporairement désactiver SSL pour tester
'verify' => false,
```

### **Webhook non reçu**
```bash
# Vérifier l'URL publique
curl -X POST https://votre-domaine.com/client/payment/callback
```

---

## 📞 Support

### **En cas de problème :**
1. **Vérifier les logs** : `storage/logs/laravel.log`
2. **Tester les clés** : Dashboard Moyasar
3. **Contacter Moyasar** : support@moyasar.com
4. **Rollback** : Remettre les clés de test

---

## 🎯 Résumé Rapide

| Action | Fichier | Changement |
|--------|---------|------------|
| **Clés API** | `.env` | `sk_test_xxx` → `sk_live_xxx` |
| **SSL** | `MoyasarService.php` | `verify: false` → `verify: true` |
| **Webhook** | Dashboard Moyasar | URL de production |
| **Tests** | Manuel | Vraie carte + 1 SAR |

**🚀 Temps estimé de migration : 15 minutes**

**✅ Aucun changement de code métier requis !**
