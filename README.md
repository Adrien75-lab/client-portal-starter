# 🚀 Client Portal Starter

Application **Symfony (backend)** + **Vue 3 / Vite / Vuetify (frontend)**, orchestrée avec **Docker Compose**.  

Fonctionnalités principales :
- ✅ Authentification (login / register / logout)
- ✅ Réinitialisation du mot de passe
- ✅ Formulaire de contact (limité à 1 envoi / heure)
- ✅ Upload de fichiers (≤ 2Mo)
- ✅ Liste et suppression des fichiers envoyés
- ✅ Badge indiquant le nombre de fichiers envoyés par utilisateur
- ✅ Cron Symfony : comptage des utilisateurs connectés par jour

---

## 📦 Prérequis

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/)

---

## ▶️ Installation & Lancement

### 1. Cloner le projet
```bash
git clone https://github.com/votre-org/client-portal-starter.git
cd client-portal-starter
```

### 2. Lancer les conteneurs
```bash
docker compose up -d --build
```

⚙️ Les services démarrés :
- **backend** → Symfony API (http://localhost:8088)
- **frontend** → Vue 3 + Vite (http://localhost:5173)
- **db** → MySQL 8 (localhost:3306)
- **mailhog** → interface SMTP de test (http://localhost:8025)
- **adminer** → UI base de données (http://localhost:8081)

---

## 🗄️ Base de données

### Créer la base
```bash
docker compose exec backend php bin/console doctrine:database:create
```

### Lancer les migrations
```bash
docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction
```

---

## 🧪 Données de test (fixtures)

Charger les données de démo :
```bash
docker compose exec backend php bin/console doctrine:fixtures:load --no-interaction
```

---

## 📧 Vérifier les emails

Tous les emails sortants sont capturés par **Mailhog**.  
👉 Interface web : [http://localhost:8025](http://localhost:8025)

---

## 🔐 Comptes de test

Après chargement des fixtures, vous pouvez utiliser :

- **Email** : `test@ex.com`  
- **Mot de passe** : `Password123!`

---

## 🛠️ Commandes utiles

### Backend (Symfony)
```bash
docker compose exec backend php bin/console cache:clear
docker compose exec backend php bin/console doctrine:schema:validate
docker compose exec backend composer require <package>
```

### Frontend (Vue.js)
```bash
docker compose exec frontend npm install
docker compose exec frontend npm run dev
docker compose exec frontend npm run build
```

### Logs
```bash
docker compose logs -f backend
docker compose logs -f frontend
```

---

## 🧹 Nettoyer l’environnement

```bash
docker compose down -v
```

(`-v` supprime aussi les volumes → réinitialise complètement la base de données)

---

## ✅ Accès rapide

- Frontend → [http://localhost:5173](http://localhost:5173)  
- Backend API → [http://localhost:8088](http://localhost:8088)  
- Mailhog → [http://localhost:8025](http://localhost:8025)  
- Adminer → [http://localhost:8081](http://localhost:8081)  

---

👨‍💻 Développé avec ❤️ pour le test technique
# client-portal-starter
