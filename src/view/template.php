<!DOCTYPE html>
<html lang="fi">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>eventhub - <?=$this->e($title)?></title>
    <link href="styles/styles.css" rel="stylesheet">
  </head>
  <body>
<header>
  <h1><a href="<?=BASEURL?>">eventhub</a></h1>

  <div class="profile">
    <?php if (isset($_SESSION['user'])): ?>

      <div class="profile-email">
        <?= htmlspecialchars($_SESSION['user']) ?>
      </div>

      <nav class="profile-links">
        <!-- Omat tapahtumat -->
        <a href="omat_tapahtumat" 
           class="profile-link profile-myevents" 
           title="Omat tapahtumat">
          <span class="profile-icon">📋</span>
          <span class="profile-text">Omat tapahtumat</span>
        </a>

        <!-- Kirjaudu ulos -->
        <a href="logout" 
           class="profile-link profile-logout" 
           title="Kirjaudu ulos">
          <span class="profile-icon">🚪</span>
          <span class="profile-text">Kirjaudu ulos</span>
        </a>

        <?php if (isset($_SESSION['admin']) && $_SESSION['admin']): ?>
          <!-- Ylläpito (vain jos tarvitset) -->
          <a href="admin" 
             class="profile-link profile-admin" 
             title="Ylläpitosivut">
            <span class="profile-icon">🛠️</span>
            <span class="profile-text">Ylläpito</span>
          </a>
        <?php endif; ?>
      </nav>

    <?php else: ?>

      <nav class="profile-links">
        <a href="kirjaudu" 
           class="profile-link profile-login" 
           title="Kirjaudu">
          <span class="profile-icon">🔑</span>
          <span class="profile-text">Kirjaudu</span>
        </a>
      </nav>

    <?php endif; ?>
  </div>
</header>

    <section>
      <?=$this->section('content')?>
    </section>
    <footer>
      <hr>
      <div>eventhub by Kurpitsa</div>
    </footer>
  </body>
</html>
