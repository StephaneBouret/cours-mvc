# 🧑‍🎓 Cloner ce cours sans jamais commiter sur le dépôt du formateur

Ce dépôt appartient au formateur.
Tu peux le cloner pour travailler, mais tu ne dois jamais pousser (push) sur ce dépôt.

## ✅ Clone puis Je veux repartir "propre" (sans l'historique Git du cours)

1. Télécharge le repo (ou clone-le),
``` bash
git clone https://github.com/StephaneBouret/cours-mvc.git
cd cours-mvc
```
2. Puis supprime le dossier .git
``` PowerShell
Remove-Item -Recurse -Force .git
```
3. Crée ton repo sur GitHub, puis ré-initialise Git :
git init
git remote add origin https://github.com/<TON_PSEUDO>/<TON_REPO>.git
```
4. Commit ton dépôt
``` bash
git add .
git commit -m "Initial commit"
git push -u origin main
```

## ✅ Vérification rapide avant tout push
Avant de pousser, fais toujours :
``` bash
git remote -v
```
Si tu vois StephaneBouret/cours-mvc en origin, STOP : tu n'es pas sur ton dépôt.
