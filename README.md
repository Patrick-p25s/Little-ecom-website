# RedCart Pro (PHP + JSON)

## Fonctionnalites
- Front-office: accueil, shop, fiche produit, panier, checkout livraison.
- Back-office admin: login securise, dashboard, gestion produits, gestion commandes, liste clients.
- Stockage JSON avec verrouillage (`flock`) et CRUD centralise.
- Theme rouge professionnel + mode clair/sombre.

## Compte admin par defaut
- Email: `admin@redcart.local`
- Mot de passe: `Admin@12345`
- Important: changez ce mot de passe avant mise en production.

## Lancer en local
```bash
php -S localhost:8000
```

## Arborescence
- `admin/` back-office protege
- `data/` fichiers JSON (`products.json`, `orders.json`, `users.json`, `admins.json`)
- `lib/` logique metier (`JsonStorage`, helpers, auth)
- `elements/` layout commun

## Checklist deploiement
- Configurer HTTPS.
- Changer les credentials admin.
- Verifier les permissions d'ecriture sur `data/`.
- Activer `display_errors=Off` en production.
- Sauvegarder regulierement les fichiers JSON.

## Evolution recommandee
- Migrer vers MySQL/PostgreSQL si charge elevée.
- Ajouter un paiement en ligne (Stripe/PayPal).
- Ajouter logs et audit admin.
# Little-ecom-website
