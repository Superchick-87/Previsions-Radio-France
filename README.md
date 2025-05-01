# Previsions-Radio-France
<h3>Affiche les prévisions d'actualité Radio France</h3>

Récupération trois par jour du flux (xml) mis à jour de Radio France et sauvagarde dans une base de donnée

Fichier distant contenant le flux : 
http://sophia.radiofrance.com/prevactu/partner/prev_xml_partner.php
Ip du server à fournir à Mme xxx en cas de changement
Récupération de l'Ip : 
https://nom-de-dommaine/Previsions-Radio-France/id-server.php

<strong>*** Back ***</strong>
</br>
Le Récurrence des majs (Radio France) :
9h15 | 13h15 | 18h15
Le Récurrence des uploads (tâche CRON - DSI SO -> M. Bardy) :
9h20 | 13h20 | 18h20
</br>
La tâche appelle cette url qui extrait un fichier xml et le place dans ./datas/
url : https://infographie.sudouest.fr/Previsions-Radio-France/getDatas.php
Une inclusion est faite via cette url de parseAndSave.php qui fait une sauvegarde en base (table : "news") afin de stocker les données et de les agréger.
"accesserver.php" à renseigner
</br>
<strong>*** Front ***</strong>
</br>
L'url index.html est reload :
- 9h22 | 13h22 | 18h22
"index.html" est traité en vuejs, tris contextuels et restrictifs
Les sources graphiques sont accessibles ici :
 ./sources/interface.ai
