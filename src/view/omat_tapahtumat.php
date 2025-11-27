<?php $this->layout('template', ['title' => 'Omat tapahtumat']) ?>

<h1>Omat tapahtumat</h1>

<?php if (empty($tapahtumat)): ?>

  <p>Et ole vielä ilmoittautunut yhteenkään tapahtumaan.</p>

<?php else: ?>

  <div class="tapahtumat">

    <!-- Otsikkorivi -->
    <div class="event-header">
      <div class="event-name">
        <div class="header-title">Tapahtuma</div>
      </div>
      <div class="event-city">
        <div class="header-title">Paikkakunta</div>
      </div>
      <div class="event-date">
        <div class="header-title">Ajankohta</div>
      </div>
      <div class="event-role">
        <div class="header-title">Rooli</div>
      </div>
      <div class="event-link">
        <div class="header-title"></div>
      </div>
    </div>

    <!-- Rivien listaus -->
    <?php foreach ($tapahtumat as $tapahtuma):

      $start = new DateTime($tapahtuma['tap_alkaa']);
      $end   = new DateTime($tapahtuma['tap_loppuu']);
    ?>

      <div class="event-row">
        <div class="event-name">
          <a href="tapahtuma?id=<?= $tapahtuma['idtapahtuma'] ?>">
            <?= htmlspecialchars($tapahtuma['nimi']) ?>
          </a>
        </div>

        <div class="event-city">
          <?= htmlspecialchars($tapahtuma['paikkakunta']) ?>
        </div>

        <div class="event-date">
          <?= $start->format('j.n.Y') ?>–<?= $end->format('j.n.Y') ?>
        </div>

<?php
  $role = $tapahtuma['rooli'];

  // Mapataan roolinimi CSS-luokkaan (ilman ääkkösiä)
  $roleClassMap = [
    'kävijä'        => 'role-kavija',
    'esiintyjä'     => 'role-esiintyja',
    'myyjä'         => 'role-myyja',
    'vapaaehtoinen' => 'role-vapaaehtoinen',
    'cosplayer'     => 'role-cosplayer',
  ];
  $roleClass = $roleClassMap[$role] ?? 'role-default';
  
?>

<div class="event-role">
  <span class="role-badge <?= $roleClass ?>">
    <?= htmlspecialchars($role) ?>
  </span>
</div>


        <div class="event-link">
          <a href="peru?id=<?= $tapahtuma['idtapahtuma'] ?>">PERU</a>
        </div>
      </div>

    <?php endforeach; ?>

  </div>

<?php endif; ?>
