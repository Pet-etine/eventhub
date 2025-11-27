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
        <?php
          if (isset($_SESSION['user'])) {
            echo "<div>$_SESSION[user]</div>";
            echo "<div><a href='omat_tapahtumat'>Omat tapahtumat</a></div>";
            echo "<div><a href='logout'>Kirjaudu ulos</a></div>";
            if (isset($_SESSION['admin']) && $_SESSION['admin']) {
              echo "<div><a href='admin'>Ylläpitosivut</a></div>";  
            }
          } else {
            echo "<div><a href='kirjaudu'>Kirjaudu</a></div>";
          }
        ?>
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
