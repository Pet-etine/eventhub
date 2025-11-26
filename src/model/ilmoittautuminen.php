<?php

require_once HELPERS_DIR . 'DB.php';

/**
 * Lisää ilmoittautuminen roolin ja muistiinpanojen kanssa.
 */
function lisaaIlmoittautuminen($idhenkilo, $idtapahtuma, $rooli = 'kävijä', $muistiinpanot = null) {
    // sallitut roolit kannan ENUMin mukaan
    $sallitut = ['esiintyjä','myyjä','kävijä','vapaaehtoinen','cosplayer'];
    if (!in_array($rooli, $sallitut, true)) {
        $rooli = 'kävijä';
    }

    return DB::run(
        "INSERT INTO ilmoittautuminen (idhenkilo, idtapahtuma, rooli, muistiinpanot)
         VALUES (?, ?, ?, ?);",
        [$idhenkilo, $idtapahtuma, $rooli, $muistiinpanot]
    );
}

/**
 * Palauttaa yhden ilmoittautumisen TÄYSINÄ tietoineen
 * (mm. rooli, muistiinpanot) tai null jos ei löydy.
 */
function haeIlmoittautuminen($idhenkilo, $idtapahtuma) {
    return DB::run(
        "SELECT *
         FROM ilmoittautuminen
         WHERE idhenkilo = ? AND idtapahtuma = ?",
        [$idhenkilo, $idtapahtuma]
    )->fetch();
}

/**
 * Poistaa yhden ilmoittautumisen.
 */
function poistaIlmoittautuminen($idhenkilo, $idtapahtuma) {
    return DB::run(
        "DELETE FROM ilmoittautuminen
         WHERE idhenkilo = ? AND idtapahtuma = ?",
        [$idhenkilo, $idtapahtuma]
    );
}

/**
 * Kaikki käyttäjän ilmoittautumiset: käytetään Omat tapahtumat -sivulla.
 */
function haeIlmoittautumisetKayttajalle($idhenkilo) {
    $sql = "SELECT t.*, i.rooli, i.muistiinpanot, i.aika
            FROM ilmoittautuminen i
            JOIN tapahtuma t ON i.idtapahtuma = t.idtapahtuma
            WHERE i.idhenkilo = ?
            ORDER BY t.tap_alkaa ASC";

    return DB::run($sql, [$idhenkilo])->fetchAll();
}
