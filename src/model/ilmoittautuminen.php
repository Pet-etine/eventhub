<?php

  require_once HELPERS_DIR . 'DB.php';

  function haeIlmoittautuminen($idhenkilo,$idtapahtuma) {
    return DB::run('SELECT * FROM ilmoittautuminen WHERE idhenkilo = ? AND idtapahtuma = ?',
                   [$idhenkilo, $idtapahtuma])->fetchAll();
  }

  function lisaaIlmoittautuminen($idhenkilo,$idtapahtuma) {
    DB::run('INSERT INTO ilmoittautuminen (idhenkilo, idtapahtuma) VALUE (?,?)',
            [$idhenkilo, $idtapahtuma]);
    return DB::lastInsertId();
  }

  function poistaIlmoittautuminen($idhenkilo, $idtapahtuma) {
    return DB::run('DELETE FROM ilmoittautuminen  WHERE idhenkilo = ? AND idtapahtuma = ?',
                   [$idhenkilo, $idtapahtuma])->rowCount();
  }
function haeIlmoittautumisetKayttajalle($idhenkilo) {
  // Haetaan kaikki tapahtumat joihin tämä käyttäjä on ilmoittautunut
  $sql = "SELECT t.*
          FROM ilmoittautuminen i
          JOIN tapahtuma t ON i.idtapahtuma = t.idtapahtuma
          WHERE i.idhenkilo = ?
          ORDER BY t.tap_alkaa ASC";

  return DB::run($sql, [$idhenkilo])->fetchAll();
}
?>
