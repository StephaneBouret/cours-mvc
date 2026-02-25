# 🧑‍🎓 Cloner ce cours sans jamais commiter sur le dépôt du formateur

Ce dépôt appartient au formateur.\
Tu peux le cloner pour travailler, mais tu ne dois **jamais pousser
(push)** sur ce dépôt.

⚠️ Si tu pushes sur le mauvais dépôt, tu risques de modifier le travail
du formateur.

------------------------------------------------------------------------

## ✅ Méthode recommandée : repartir "propre" (sans l'historique Git)

Cette méthode te permet d'avoir **ton propre dépôt indépendant**.

------------------------------------------------------------------------

## 1️⃣ Télécharger ou cloner le repo

``` bash
git clone https://github.com/StephaneBouret/cours-mvc.git
cd cours-mvc
```

------------------------------------------------------------------------

## 2️⃣ Supprimer le dossier `.git`

Dans ton dossier `cours-mvc`, supprime le dossier `.git`.

👉 Sous Windows PowerShell :

``` bash
Remove-Item -Recurse -Force .git
```

✅ Le projet n'est maintenant plus lié au dépôt du formateur.

------------------------------------------------------------------------

## 3️⃣ Créer TON dépôt GitHub puis réinitialiser Git

Crée d'abord un **nouveau dépôt vide** sur ton GitHub, puis exécute :

``` bash
git init
git remote add origin https://github.com/<TON_PSEUDO>/<TON_REPO>.git
```

------------------------------------------------------------------------

## 4️⃣ Premier commit

``` bash
git add .
git commit -m "Initial commit"
git push -u origin main
```

------------------------------------------------------------------------

## ✅ Vérification rapide avant tout push (TRÈS IMPORTANT)

Avant de pousser, fais toujours :

``` bash
git remote -v
```

### ✔️ Bon résultat attendu

Tu dois voir **ton dépôt à toi**.

### ❌ Mauvais résultat

Si tu vois `StephaneBouret/cours-mvc` :

👉 **STOP immédiatement** --- tu es encore relié au dépôt du formateur.
