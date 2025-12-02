<?php 
// Asetetaan sivupohja ja sivun title
$this->layout('template', ['title' => 'Tulevat tapahtumat']) 
?>

<h1>Tulevat tapahtumat</h1>

<div class="tapahtumat">

  <!-- Otsakerivi: sarakkeet + lajittelulinkit -->
  <div class="event-header">

    <!-- Tapahtuman nimi + lajittelu A–Ö / Ö–A -->
    <div class="event-name">
      <div class="header-title">Tapahtuma</div>
      <div class="sort-buttons">
        <a href="<?= BASEURL ?>tapahtumat?jarj=nimi_asc">A–Ö</a>
        <a href="<?= BASEURL ?>tapahtumat?jarj=nimi_desc">Ö–A</a>
      </div>
    </div>

    <!-- Paikkakunta + lajittelu A–Ö / Ö–A -->
    <div class="event-city">
      <div class="header-title">Paikkakunta</div>
      <div class="sort-buttons">
        <a href="<?= BASEURL ?>tapahtumat?jarj=paikkakunta_asc">A–Ö</a>
        <a href="<?= BASEURL ?>tapahtumat?jarj=paikkakunta_desc">Ö–A</a>
      </div>
    </div>

    <!-- Ajankohta + lajittelu nouseva / laskeva -->
    <div class="event-date">
      <div class="header-title">Ajankohta</div>
      <div class="sort-buttons">
        <a href="<?= BASEURL ?>tapahtumat?jarj=pvm_asc">▲</a>
        <a href="<?= BASEURL ?>tapahtumat?jarj=pvm_desc">▼</a>
      </div>
    </div>

    <!-- HUOM: ei enää erillistä event-link -saraketta -->
  </div>

  <?php foreach ($tapahtumat as $tapahtuma): ?>

    <?php
      // Muutetaan alku- ja loppuaika DateTime-olioiksi
      $start = new DateTime($tapahtuma['tap_alkaa']);
      $end   = new DateTime($tapahtuma['tap_loppuu']);
    ?>

    <div class="event-row">

      <!-- Tapahtuman nimi on nyt klikattava linkki tapahtumasivulle -->
      <div class="event-name">
        <a 
          href="<?= BASEURL ?>tapahtuma?id=<?= (int)$tapahtuma['idtapahtuma'] ?>" 
          class="event-name-link"
        >
          <?= htmlspecialchars($tapahtuma['nimi']) ?>
        </a>
      </div>

      <!-- Paikkakunta -->
      <div class="event-city">
        <?= htmlspecialchars($tapahtuma['paikkakunta']) ?>
      </div>

      <!-- Ajankohta (esim. 3.5.2025–4.5.2025) -->
      <div class="event-date">
        <?= $start->format('j.n.Y') ?>–<?= $end->format('j.n.Y') ?>
      </div>

    </div>

  <?php endforeach; ?>

</div>
