<?php $this->layout('template', ['title' => $tapahtuma['nimi']]) ?>

<?php
  $start = new DateTime($tapahtuma['tap_alkaa']);
  $end = new DateTime($tapahtuma['tap_loppuu']);
?>

<h1><?= htmlspecialchars($tapahtuma['nimi']) ?></h1>

<div><?= nl2br(htmlspecialchars($tapahtuma['kuvaus'])) ?></div>
<div>Alkaa: <?= $start->format('j.n.Y G:i') ?></div>
<div>Loppuu: <?= $end->format('j.n.Y G:i') ?></div>

<?php if ($loggeduser): ?>

  <?php if (!$ilmoittautuminen): ?>

    <!-- ILMOITTAUTUMISLOMAKE ROOLILLA -->
    <form method="post" action="ilmoittaudu?id=<?= $tapahtuma['idtapahtuma'] ?>" class="ilmo-form" style="margin-top:1rem;">

      <p>Valitse rooli tapahtumassa:</p>

      <label>
        <input type="radio" name="rooli" value="kävijä" checked>
        Kävijä
      </label>
      <label>
        <input type="radio" name="rooli" value="esiintyjä">
        Esiintyjä
      </label>
      <label>
        <input type="radio" name="rooli" value="myyjä">
        Myyjä
      </label>
      <label>
        <input type="radio" name="rooli" value="vapaaehtoinen">
        Vapaaehtoinen
      </label>
      <label>
        <input type="radio" name="rooli" value="cosplayer">
        Cosplayer
      </label>

      <p style="margin-top:0.8rem;">Muistiinpanot (esim. pöydän nimi, erikoistoiveet):</p>
      <textarea name="muistiinpanot" rows="3" style="width:100%;"></textarea>

      <div class="flexarea">
        <input type="submit" value="ILMOITTAUDU">
      </div>
    </form>

  <?php else: ?>

    <!-- NÄYTÄ OMA ROOLI + MAHDOLLISET MUISTIINPANOT -->
    <div class="flexarea" style="margin-top:1rem;">
      <div>Olet ilmoittautunut tapahtumaan roolissa:
        <strong><?= htmlspecialchars($ilmoittautuminen['rooli']) ?></strong>
      </div>

      <?php if (!empty($ilmoittautuminen['muistiinpanot'])): ?>
        <div style="margin-top:0.5rem;">
          Muistiinpanosi:<br>
          <?= nl2br(htmlspecialchars($ilmoittautuminen['muistiinpanot'])) ?>
        </div>
      <?php endif; ?>

      <a href="peru?id=<?= $tapahtuma['idtapahtuma'] ?>" class="button" style="margin-top:1rem;">
        PERU ILMOITTAUTUMINEN
      </a>
    </div>

  <?php endif; ?>

<?php endif; ?>
