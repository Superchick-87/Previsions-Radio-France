<?php

$url_fichier_distant = "http://sophia.radiofrance.com/prevactu/partner/prev_xml_partner.php";
// $url_fichier_distant = "http://localhost:8888/Previsions-Radio-France-V2/datas.php";

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url_fichier_distant);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

$contenu_page = curl_exec($curl);
$info = curl_getinfo($curl);

if (curl_errno($curl)) {
    echo "Erreur cURL (" . curl_errno($curl) . "): " . curl_error($curl) . "<br>";
} elseif ($contenu_page === false) {
    echo "Erreur : Impossible de récupérer le contenu de la page.";
} else {
    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $contenu_page = substr($contenu_page, $header_size);

    $content_type = $info['content_type'];
    echo "Content-Type: " . $content_type . "<br>";

    if (stripos($content_type, 'text/html') !== false) {
        $contenu_page = mb_convert_encoding($contenu_page, 'UTF-8', 'windows-1252');
    }

    $contenu_page = trim($contenu_page);

    $balise_debut = "<NEWS>";
    $balise_fin = "</NEWS>";
    $position_debut = strpos($contenu_page, $balise_debut);
    $position_fin = strpos($contenu_page, $balise_fin);

    if ($position_debut !== false && $position_fin !== false && $position_debut < $position_fin) {
        $contenu_xml = substr($contenu_page, $position_debut, $position_fin - $position_debut + strlen($balise_fin));

        $dossier_local = __DIR__ . "/datas";
        if (!is_dir($dossier_local)) {
            mkdir($dossier_local, 0777, true);
        }

        $latestFile = $dossier_local . "/latest.xml";

        if (file_exists($latestFile)) {
            $latestData = json_decode(file_get_contents($latestFile), true);
            if (isset($latestData['latest'])) {
                $ancien_fichier = __DIR__ . "/" . $latestData['latest'];
                if (file_exists($ancien_fichier)) {
                    if (unlink($ancien_fichier)) {
                        echo "<br><strong style='color:red;'>Ancien fichier supprimé : </strong><br>" . $ancien_fichier;
                    } else {
                        echo "<br><strong style='color:red;'>Erreur lors de la suppression de l'ancien fichier : " . $ancien_fichier . "</strong><br>";
                    }
                } else {
                    echo "<br><strong style='color:red;'>Le fichier ancien XML n'existe pas : " . $ancien_fichier . "</strong><br>";
                }
            }
        }

        // Générer un nouveau fichier XML avec un nom unique
        $dateTime = date('Ymd_His');
        $nom_fichier = "datas_" . $dateTime . ".xml";
        $chemin_fichier = $dossier_local . "/" . $nom_fichier;

        file_put_contents($chemin_fichier, $contenu_xml);

        echo "<br><br><strong style='color:green;'>Nouveau fichier XML enregistré : </strong><br>" . $chemin_fichier;

        // Enregistrer dans latest.xml avec le chemin relatif complet
        $latestData = [
            "latest" => "datas/" . $nom_fichier,
            "nocache" => time()
        ];
        file_put_contents($latestFile, json_encode($latestData));

        // ✅ Appel à l'inclusion de traitement XML vers base
        include_once(__DIR__ . "/parseAndSave.php");
        enregistrerXmlDansBDD($contenu_xml); // Appel à la fonction du fichier inclus
    } else {
        echo "Impossible de trouver les balises <NEWS> et </NEWS> dans le contenu de la page.";
    }
}

curl_close($curl);
