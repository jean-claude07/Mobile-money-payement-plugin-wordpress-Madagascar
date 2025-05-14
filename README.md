
# 📱 Mobile Money payement plugin wordpress Madagascar

**Version du plugin** : 1.2.1  
**Compatibilité WordPress** : 5.0+  
**Compatibilité WooCommerce** : 6.0+

---

## 📝 Présentation

**Mobile Money Madagascar** est un plugin WooCommerce qui permet l'intégration des principaux services de paiement mobile à Madagascar : **MVola**, **Orange Money** et **Airtel Money**.  
Conçu pour les sites WordPress, il permet aux e-commerçants malgaches de proposer une solution de paiement sécurisée, rapide et compatible avec tous les appareils mobiles.

---

## ✨ Fonctionnalités

### 🔐 Paiements mobiles sécurisés
- 💳 Compatibilité avec **MVola**, **Orange Money** et **Airtel Money**
- 🔄 Paiements en **temps réel**
- 📱 Interface responsive et intuitive
- 🧾 Génération automatique des reçus

### 🛡️ Sécurité avancée
- 🔐 Chiffrement des données sensibles
- 🛡️ Protection contre les attaques CSRF
- 🔍 Détection de fraudes
- 📊 Journalisation des événements critiques

### ⚙️ Interface d'administration complète
- 📊 Tableau de bord analytique
- 📈 Statistiques en temps réel
- 💰 Gestion des transactions et remboursements
- 🧩 Paramétrage des passerelles de paiement
- 🖥️ UI claire et intuitive

---

## 📋 Prérequis techniques

- WordPress **5.0** ou plus
- WooCommerce **6.0** ou plus
- PHP **7.2** ou supérieur
- Extension **OpenSSL** activée
- Site en **HTTPS** (obligatoire)

---

## 💻 Installation

### Méthode manuelle

```bash
cd /var/www/html/wordpress/wp-content/plugins/
git clone https://github.com/jean-claude07/Mobile-money-payement-plugin-wordpress-Madagascar.git
```

### Depuis l’interface WordPress

1. Allez dans **Extensions > Ajouter**
2. Cliquez sur **Téléverser une extension**
3. Sélectionnez l’archive `.zip` du plugin
4. Cliquez sur **Installer maintenant**, puis **Activer**

---

## ⚙️ Configuration du plugin

### Accès
`WooCommerce > Réglages > Paiements`

### Paramétrage des opérateurs

#### MVola
- API URL
- Clé API
- Numéro marchand
- Mode **sandbox** ou **production**

#### Orange Money
- API URL
- Clé API
- Numéro marchand
- Mode **sandbox** ou **production**

#### Airtel Money
- API URL
- Clé API
- Numéro marchand
- Mode **sandbox** ou **production**

---

## 🔧 Utilisation

### Pour les administrateurs
- 🔁 Gestion des paiements : `Mobile Money > Transactions`
- 📊 Statistiques : `Mobile Money > Tableau de bord`
- ⚙️ Réglages : `Mobile Money > Réglages`

### Pour les clients
- Paiement mobile disponible à la commande
- Choix de l’opérateur souhaité
- Saisie du numéro de téléphone
- Confirmation via téléphone mobile

---

## 🧪 Débogage & Logs

Les fichiers logs sont situés dans :
```
wp-content/plugins/mobil-money-plugin/logs/
```

---

## 🛠️ Support technique

- 📧 Email : [contact@dizitalizeo.com](mailto:contact@dizitalizeo.com)
- 🌐 Site : [www.dizitalizeo.com](https://www.dizitalizeo.com)
- 📞 Téléphone : +261 34 60 813 51

---

## 🤝 Contribuer

Toutes les contributions sont les bienvenues !

1. Forkez le projet
2. Créez une branche : `feature/nom-fonctionnalité`
3. Committez vos modifications
4. Poussez vers votre dépôt forké
5. Créez une **Pull Request**

---

## 📌 Journal des versions

### 🆕 v1.2.1 – *30 avril 2025*
- ✅ Compatibilité HPOS WooCommerce
- 🐞 Correction de bugs critiques
- 🔒 Renforcement de la sécurité

### v1.1.0
- 📊 Ajout du tableau de bord
- 🔄 Amélioration des confirmations automatiques

### v1.0.0
- 🚀 Version initiale stable
- 💳 Support de base MVola, Orange & Airtel

---

## 📜 Licence

Ce plugin est distribué sous **GPL v2** ou version ultérieure.

---

## 👤 Auteur

**RAKOTONARIVO Jean Claude**  
🌐 [www.dizitalizeo.com](https://www.dizitalizeo.com)  
🐙 [GitHub : jean-claude07](https://github.com/jean-claude07)  
💼 LinkedIn : RAKOTONARIVO Jean Claude

---

## ⚠️ Recommandations importantes

- Ayez une connexion stable pour toutes les opérations critiques
- Effectuez vos tests sur un environnement sandbox avant la production
- Sauvegardez régulièrement votre base de données et vos fichiers
- Maintenez le plugin à jour pour bénéficier des dernières améliorations

---

© 2025 Jean Claude RAKOTONARIVO – Tous droits réservés.
