<?php

function enregistrerXmlDansBDD($contenu_xml) {
    include (dirname(__FILE__).'/includes/accesserver.php');

    try {
        // Connexion à la base de données
        $pdo = new PDO("mysql:host=$serveur;dbname=$database;charset=utf8mb4", $login, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base : " . $e->getMessage());
    }

    // Vérifier si $contenu_xml est bien défini
    if (empty($contenu_xml)) {
        die("Le contenu XML n'est pas défini.");
    }

    // Charger le XML
    $xml = simplexml_load_string($contenu_xml);
    if (!$xml) {
        die("Erreur lors du chargement du XML.");
    }

    // Fonction pour convertir les dates ISO 8601 en format compatible MySQL
    function formatDate($isoDate) {
        try {
            $dt = new DateTime($isoDate);
            $dt->setTimezone(new DateTimeZone('UTC'));
            return $dt->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return null;
        }
    }

    foreach ($xml->ITEM as $item) {
        $uid     = (int) $item->UID;
        $update  = formatDate((string) $item->UPDATE);
        $start   = formatDate((string) $item->START);
        $end     = formatDate((string) $item->END);
        $title   = html_entity_decode(trim((string) $item->TITLE), ENT_QUOTES | ENT_XML1, 'UTF-8');
        $content = html_entity_decode(trim((string) $item->CONTENT), ENT_QUOTES | ENT_XML1, 'UTF-8');
        $type    = trim((string) $item->TYPE);
        $subtype = trim((string) $item->SUBTYPE);
        $level   = (int) $item->LEVEL;
        $source  = trim((string) $item->SOURCE);

        // Requête SQL avec mise à jour si doublon UID
        $sql = "
            INSERT INTO news (uid, updated_at, start_at, end_at, title, content, type, subtype, level, source)
            VALUES (:uid, :updated_at, :start_at, :end_at, :title, :content, :type, :subtype, :level, :source)
            ON DUPLICATE KEY UPDATE 
                updated_at = VALUES(updated_at),
                start_at = VALUES(start_at),
                end_at = VALUES(end_at),
                title = VALUES(title),
                content = VALUES(content),
                type = VALUES(type),
                subtype = VALUES(subtype),
                level = VALUES(level),
                source = VALUES(source)
        ";

        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ':uid' => $uid,
                ':updated_at' => $update,
                ':start_at' => $start,
                ':end_at' => $end,
                ':title' => $title,
                ':content' => $content,
                ':type' => $type,
                ':subtype' => $subtype,
                ':level' => $level,
                ':source' => $source
            ]);
            echo "✅ UID $uid traité.<br>";
        } catch (PDOException $e) {
            echo "❌ Erreur sur UID $uid : " . $e->getMessage() . "<br>";
        }
    }
}
