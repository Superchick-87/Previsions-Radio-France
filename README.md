# Previsions-Radio-France
Affiche les prévisions d'actualité Radio France

Récupération trois par jour du flux (xml) mis à jour de Radio France et sauvagarde dans une base de donnée

Fichier distant = "http://sophia.radiofrance.com/prevactu/partner/prev_xml_partner.php";
Ip du server à fournir à Mme xxx en cas de changement
Récupération de l'Ip : https://nom-de-dommaine/Previsions-Radio-France/id-server.php

*** Back ***
Le Récurrence des majs (Radio France) :
- 9h15
- 13h15
- 18h15

Le Récurrence des uploads (tâche CRON - DSI SO -> M. Bardy) :
- 9h20
- 13h20
- 18h20
La tâche appelle cette url qui  le fichier xml dans ./datas/
https://infographie.sudouest.fr/Previsions-Radio-France/getDatas.php
Une inclusion est faite via cette url de parseAndSave.php qui fait une sauvegarde en base (table : "news") afin de stocker toutes les infos et de les agréger.
"accesserver.php" à renseigner

*** Front ***
L'url index.html est reload :
- 9h20
- 13h20
- 18h20

"index.html" est traité en vuejs, tris contextuels et restrictifs
Les sources graphiques sont accessibles ici :
 ./sources/interface.ai
