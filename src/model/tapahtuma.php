<?php

require_once HELPERS_DIR . 'DB.php';

function haeTapahtumat($jarj = 'pvm_asc') {

  // Sallitut järjestystavat
  $jarjestykset = [
    'nimi_asc'         => 'nimi ASC',
    'nimi_desc'        => 'nimi DESC',
    'paikkakunta_asc'  => 'paikkakunta ASC',
    'paikkakunta_desc' => 'paikkakunta DESC',
    'pvm_asc'          => 'tap_alkaa ASC',
    'pvm_desc'         => 'tap_alkaa DESC',
  ];


  if (!isset($jarjestykset[$jarj])) {
    $jarj = 'pvm_asc';
  }

  $orderBy = $jarjestykset[$jarj];

  return DB::run("SELECT * FROM tapahtuma ORDER BY $orderBy;")->fetchAll();
}

function haeTapahtuma($id) {
  return DB::run('SELECT * FROM tapahtuma WHERE idtapahtuma = ?;', [$id])->fetch();
}
?>
