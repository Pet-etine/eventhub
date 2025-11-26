<?php $this->layout('template', ['title' => 'Tulevat tapahtumat']) ?>

<h1>Tulevat tapahtumat</h1>


<div class='tapahtumat'>
<div class="event-header">
  <div class="event-name">
    Tapahtuma 
<a href="<?= BASEURL ?>tapahtumat?jarj=nimi_asc" 
   class="<?= $jarj == 'nimi_asc' ? 'active' : '' ?>">▲</a>
<a href="<?= BASEURL ?>tapahtumat?jarj=nimi_desc" 
   class="<?= $jarj == 'nimi_desc' ? 'active' : '' ?>">▼</a>
  </div>

  <div class="event-city">
    Paikkakunta 
<a href="<?= BASEURL ?>tapahtumat?jarj=paikkakunta_asc" 
   class="<?= $jarj == 'paikkakunta_asc' ? 'active' : '' ?>">▲</a>
<a href="<?= BASEURL ?>tapahtumat?jarj=paikkakunta_desc" 
   class="<?= $jarj == 'paikkakunta_desc' ? 'active' : '' ?>">▼</a>
  </div>

  <div class="event-date">
    Ajankohta 
<a href="<?= BASEURL ?>tapahtumat?jarj=pvm_asc" 
   class="<?= $jarj == 'pvm_asc' ? 'active' : '' ?>">▲</a>
<a href="<?= BASEURL ?>tapahtumat?jarj=pvm_desc" 
   class="<?= $jarj == 'pvm_desc' ? 'active' : '' ?>">▼</a>
  </div>

  <div class="event-link"></div>
</div> 
<?php

foreach ($tapahtumat as $tapahtuma) {

  $start = new DateTime($tapahtuma['tap_alkaa']);
  $end   = new DateTime($tapahtuma['tap_loppuu']);

  echo "<div class='event-row'>";
    echo "<div class='event-name'>$tapahtuma[nimi]</div>";
    echo "<div class='event-city'>$tapahtuma[paikkakunta]</div>";
    echo "<div class='event-date'>" .
          $start->format('j.n.Y') . "–" .
          $end->format('j.n.Y') .
         "</div>";
    echo "<div class='event-link'>
            <a href='tapahtuma?id=" . $tapahtuma['idtapahtuma'] . "'>TIEDOT</a>
          </div>";
  echo "</div>";
}

?>
</div>
