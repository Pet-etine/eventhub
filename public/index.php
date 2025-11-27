<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Suoritetaan projektin alustusskripti.
require_once '../src/init.php';

session_start();

$loggeduser = null;
if (isset($_SESSION['user'])) {
  require_once MODEL_DIR . 'henkilo.php';
  $rows = haeHenkiloSahkopostilla($_SESSION['user']);
  $loggeduser = $rows ? array_shift($rows) : null;
}

// Käytä ensisijaisesti sessioon tallennettua roolia (nopea), muuten DB:stä haettu
$isAdmin = (isset($_SESSION['rooli']) && $_SESSION['rooli'] === 'admin')
        || ($loggeduser && isset($loggeduser['rooli']) && $loggeduser['rooli'] === 'admin');


// Siistitään polku urlin alusta ja mahdolliset parametrit urlin lopusta.
$request = str_replace($config['urls']['baseUrl'], '', $_SERVER['REQUEST_URI']);
$request = strtok($request, '?');

// Lisätään eteen / ja poistetaan mahdolliset tuplaviivat
$request = '/' . ltrim($request, '/');

// Luodaan uusi Plates-olio ja kytketään se sovelluksen sivupohjiin.
$templates = new League\Plates\Engine(TEMPLATE_DIR);

// Selvitetään mitä sivua on kutsuttu ja suoritetaan sivua vastaava käsittelijä.
switch ($request) {

  // ← tämä puuttui: etusivu ohjataan listaan
  case '/':
  header("Location: " . $config['urls']['baseUrl'] . "tapahtumat");
  exit;

case '/tapahtumat':
  require_once MODEL_DIR . 'tapahtuma.php';

  // luetaan järjestys URL:sta, esim. ?jarj=nimi_asc
  $jarj = isset($_GET['jarj']) ? $_GET['jarj'] : 'pvm_asc';

  $tapahtumat = haeTapahtumat($jarj);

  echo $templates->render('tapahtumat', [
    'tapahtumat' => $tapahtumat,
    'jarj'       => $jarj
  ]);
  break;



    case '/tapahtuma':
      require_once MODEL_DIR . 'tapahtuma.php';
      require_once MODEL_DIR . 'ilmoittautuminen.php';
      $tapahtuma = haeTapahtuma($_GET['id']);
      if ($tapahtuma) {
        if ($loggeduser) {
          $ilmoittautuminen = haeIlmoittautuminen($loggeduser['idhenkilo'],$tapahtuma['idtapahtuma']);
        } else {
          $ilmoittautuminen = NULL;
        }
        echo $templates->render('tapahtuma',['tapahtuma' => $tapahtuma,
                                             'ilmoittautuminen' => $ilmoittautuminen,
                                             'loggeduser' => $loggeduser]);
      } else {
        echo $templates->render('tapahtumanotfound');
      }
      break;

    case '/lisaa_tili':
      if (isset($_POST['laheta'])) {
        $formdata = cleanArrayData($_POST);
        require_once CONTROLLER_DIR . 'tili.php';
        $tulos = lisaaTili($formdata,$config['urls']['baseUrl']);
      if ($tulos['status'] == "200") {
        echo $templates->render('tili_luotu', ['formdata' => $formdata]);
        break;
      }
      echo $templates->render('lisaa_tili', ['formdata' => $formdata, 'error' => $tulos['error']]);
      break;
    } else {
      echo $templates->render('lisaa_tili', ['formdata' => [], 'error' => []]);
      break;
    }

case "/kirjaudu":
  if (isset($_POST['laheta'])) {
    require_once CONTROLLER_DIR . 'kirjaudu.php';
    if (tarkistaKirjautuminen($_POST['email'], $_POST['salasana'])) {
      require_once MODEL_DIR . 'henkilo.php';
      $rows = haeHenkiloSahkopostilla($_POST['email']);  
      $user = $rows ? array_shift($rows) : null;

      if ($user && $user['vahvistettu']) {
        session_regenerate_id(true);
        $_SESSION['user']  = $user['email'];
        $_SESSION['rooli'] = $user['rooli'];              
        header("Location: " . $config['urls']['baseUrl']);
        exit;
      } else {
        echo $templates->render('kirjaudu', [
          'error' => ['virhe' => 'Tili on vahvistamatta! Ole hyvä, ja vahvista tili.']
        ]);
      }
    } else {
      echo $templates->render('kirjaudu', [
        'error' => ['virhe' => 'Väärä käyttäjätunnus tai salasana!']
      ]);
    }
  } else {
    echo $templates->render('kirjaudu', ['error' => []]);
  }
  break;



    case "/logout":
      require_once CONTROLLER_DIR . 'kirjaudu.php';
      logout();
      header("Location: " . $config['urls']['baseUrl']);
      break;

case '/ilmoittaudu':
  if (isset($_GET['id']) && $loggeduser) {
    require_once MODEL_DIR . 'ilmoittautuminen.php';

    $idtapahtuma = (int)$_GET['id'];

    $rooli = $_POST['rooli'] ?? 'kävijä';
    $muistiinpanot = isset($_POST['muistiinpanot']) ? trim($_POST['muistiinpanot']) : null;

    $sallitut = ['esiintyjä','myyjä','kävijä','vapaaehtoinen','cosplayer'];
    if (!in_array($rooli, $sallitut, true)) {
      $rooli = 'kävijä';
    }

    lisaaIlmoittautuminen(
      $loggeduser['idhenkilo'],
      $idtapahtuma,
      $rooli,
      $muistiinpanot ?: null
    );

    header("Location: tapahtuma?id=$idtapahtuma");
    exit;
  } else {
    header("Location: tapahtumat");
    exit;
  }
  break;


case '/paivita_ilmoittautuminen':
  if (isset($_GET['id']) && $loggeduser) {
    require_once MODEL_DIR . 'ilmoittautuminen.php';

    $idtapahtuma = (int)$_GET['id'];

    $rooli = $_POST['rooli'] ?? 'kävijä';
    $muistiinpanot = isset($_POST['muistiinpanot']) ? trim($_POST['muistiinpanot']) : null;

    $sallitut = ['esiintyjä','myyjä','kävijä','vapaaehtoinen','cosplayer'];
    if (!in_array($rooli, $sallitut, true)) {
      $rooli = 'kävijä';
    }

    paivitaIlmoittautuminen(
      $loggeduser['idhenkilo'],
      $idtapahtuma,
      $rooli,
      $muistiinpanot ?: null
    );

    header("Location: tapahtuma?id=$idtapahtuma");
    exit;
  } else {
    header("Location: tapahtumat");
    exit;
  }
  break;




    case '/peru':
      if ($_GET['id']) {
        require_once MODEL_DIR . 'ilmoittautuminen.php';
        $idtapahtuma = $_GET['id'];
        if ($loggeduser) {
          poistaIlmoittautuminen($loggeduser['idhenkilo'],$idtapahtuma);
        }
        header("Location: tapahtuma?id=$idtapahtuma");
      } else {
        header("Location: tapahtumat");  
      }
      break;
          case "/vahvista":
      if (isset($_GET['key'])) {
        $key = $_GET['key'];
        require_once MODEL_DIR . 'henkilo.php';
        if (vahvistaTili($key)) {
          echo $templates->render('tili_aktivoitu');
        } else {
          echo $templates->render('tili_aktivointi_virhe');
        }
      } else {
        header("Location: " . $config['urls']['baseUrl']);
      }
      break;
    case "/tilaa_vaihtoavain":
      $formdata = cleanArrayData($_POST);
      // Tarkistetaan, onko lomakkeelta lähetetty tietoa.
      if (isset($formdata['laheta'])) {    
  
        require_once MODEL_DIR . 'henkilo.php';
        // Tarkistetaan, onko lomakkeelle syötetty käyttäjätili olemassa.
        $user = haeHenkilo($formdata['email']);
        if ($user) {
          // Käyttäjätili on olemassa.
          // Luodaan salasanan vaihtolinkki ja lähetetään se sähköpostiin.
          require_once CONTROLLER_DIR . 'tili.php';
          $tulos = luoVaihtoavain($formdata['email'],$config['urls']['baseUrl']);
          if ($tulos['status'] == "200") {
            // Vaihtolinkki lähetty sähköpostiin, tulostetaan ilmoitus.
            echo $templates->render('tilaa_vaihtoavain_lahetetty');
            break;
          }
          // Vaihtolinkin lähetyksessä tapahtui virhe, tulostetaan
          // yleinen virheilmoitus.
          echo $templates->render('virhe');
          break;
        } else {
          // Tunnusta ei ollut, tulostetaan ympäripyöreä ilmoitus.
          echo $templates->render('tilaa_vaihtoavain_lahetetty');
          break;
        }

  
      } else {
        // Lomakeelta ei ole lähetetty tietoa, tulostetaan lomake.
        echo $templates->render('tilaa_vaihtoavain_lomake');
      }
      break;
    case "/reset":
      // Otetaan vaihtoavain talteen.
      $resetkey = $_GET['key'];

      // Seuraavat tarkistukset tarkistavat, että onko vaihtoavain
      // olemassa ja se on vielä aktiivinen. Jos ei, niin tulostetaan
      // käyttäjälle virheilmoitus ja poistutaan.
      require_once MODEL_DIR . 'henkilo.php';
      $rivi = tarkistaVaihtoavain($resetkey);
      if ($rivi) {
        // Vaihtoavain löytyi, tarkistetaan onko se vanhentunut.
        if ($rivi['aikaikkuna'] < 0) {
          echo $templates->render('reset_virhe');
          break;
        }
      } else {
        echo $templates->render('reset_virhe');
        break;
      }

      // Vaihtoavain on voimassa, tarkistetaan onko lomakkeen kautta
      // syötetty tietoa.
      $formdata = cleanArrayData($_POST);
      if (isset($formdata['laheta'])) {

        // Lomakkeelle on syötetty uudet salasanat, annetaan syötteen
        // käsittely kontrollerille.
        require_once CONTROLLER_DIR . 'tili.php';
        $tulos = resetoiSalasana($formdata,$resetkey);
        // Tarkistetaan kontrollerin tekemän salasanaresetoinnin lopputulos.
        if ($tulos['status'] == "200") {
          // Salasana vaihdettu, tulostetaan ilmoitus.
          echo $templates->render('reset_valmis');
          break;
        }
        // Salasanan vaihto ei onnistunut, tulostetaan lomake virhetekstin kanssa.
        echo $templates->render('reset_lomake', ['error' => $tulos['error']]);
        break;


      } else {
        // Lomakkeen tietoja ei ole vielä täytetty, tulostetaan lomake.
        echo $templates->render('reset_lomake', ['error' => '']);
        break;
      }

      break;
case (bool)preg_match('/\/admin.*/', $request):
  if ($isAdmin) {
    require_once MODEL_DIR . 'henkilo.php';
    $kayttajat = haeKaikkiKayttajat();
    echo $templates->render('admin', ['kayttajat' => $kayttajat, 'loggeduser' => $loggeduser]);
  } else {
    echo $templates->render('admin_ei_oikeuksia');
  }
  break;
case '/omat_tapahtumat':
  // vain kirjautuneelle
  if (!$loggeduser) {
    header("Location: " . $config['urls']['baseUrl'] . "/kirjaudu");
    exit;
  }

  require_once MODEL_DIR . 'ilmoittautuminen.php';
  $omatTapahtumat = haeIlmoittautumisetKayttajalle($loggeduser['idhenkilo']);

  echo $templates->render('omat_tapahtumat', [
    'tapahtumat' => $omatTapahtumat,
    'loggeduser' => $loggeduser
  ]);
  break;
}
