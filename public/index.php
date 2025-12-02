<?php
// Näytetään kaikki virheet kehityksen aikana
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Suoritetaan projektin alustusskripti.
require_once '../src/init.php';

// Käynnistetään sessio ennen $_SESSION-käsittelyjä
session_start();

// ----------------------------------------------------------------------------------
// Kirjautuneen käyttäjän ja roolin selvittäminen
// ----------------------------------------------------------------------------------

$loggeduser = null;

if (isset($_SESSION['user'])) {
    require_once MODEL_DIR . 'henkilo.php';

    // Haetaan käyttäjä sähköpostin perusteella
    $rows       = haeHenkiloSahkopostilla($_SESSION['user']);
    $loggeduser = $rows ? array_shift($rows) : null;
}

// Käytä ensisijaisesti sessioon tallennettua roolia (nopea), muuten DB:stä haettu
$isAdmin =
    (isset($_SESSION['rooli']) && $_SESSION['rooli'] === 'admin')
    || ($loggeduser && isset($loggeduser['rooli']) && $loggeduser['rooli'] === 'admin');

// ----------------------------------------------------------------------------------
// URL-polun siistiminen
// ----------------------------------------------------------------------------------

// Poistetaan baseUrl pyynnön alusta ja GET-parametrit lopusta
$request = str_replace($config['urls']['baseUrl'], '', $_SERVER['REQUEST_URI']);
$request = strtok($request, '?'); // ottaa URL:n polkuosan ennen kysymysmerkkiä

// Lisätään varmuuden vuoksi eteen / ja poistetaan ylimääräiset vinoviivat
$request = '/' . ltrim($request, '/');

// ----------------------------------------------------------------------------------
// Plates-templatemootorin alustaminen
// ----------------------------------------------------------------------------------

$templates = new League\Plates\Engine(TEMPLATE_DIR);

// ----------------------------------------------------------------------------------
// Admin-reitit (kaikki /admin... ohjataan tähän)
// ----------------------------------------------------------------------------------

// Koska switch-case ei toimi hyvin preg_matchin kanssa, hoidetaan admin-reitit erikseen
if (preg_match('#^/admin#', $request)) {
    if ($isAdmin) {
        require_once MODEL_DIR . 'henkilo.php';
        $kayttajat = haeKaikkiKayttajat();

        echo $templates->render('admin', [
            'kayttajat'  => $kayttajat,
            'loggeduser' => $loggeduser,
        ]);
    } else {
        echo $templates->render('admin_ei_oikeuksia');
    }
    exit;
}

// ----------------------------------------------------------------------------------
// Varsinainen reititys
// ----------------------------------------------------------------------------------

switch ($request) {

    // Etusivu -> ohjataan tapahtumalistaan
    case '/':
        header("Location: " . $config['urls']['baseUrl'] . "tapahtumat");
        exit;

    // Tapahtumien listaussivu
    case '/tapahtumat':
        require_once MODEL_DIR . 'tapahtuma.php';

        // Luetaan järjestys URL:sta, esim. ?jarj=nimi_asc
        $jarj = isset($_GET['jarj']) ? $_GET['jarj'] : 'pvm_asc';

        $tapahtumat = haeTapahtumat($jarj);

        echo $templates->render('tapahtumat', [
            'tapahtumat' => $tapahtumat,
            'jarj'       => $jarj,
        ]);
        break;

    // Yksittäisen tapahtuman sivu
    case '/tapahtuma':
        require_once MODEL_DIR . 'tapahtuma.php';
        require_once MODEL_DIR . 'ilmoittautuminen.php';

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $tapahtuma = haeTapahtuma($id);

        if ($tapahtuma) {
            if ($loggeduser) {
                $ilmoittautuminen = haeIlmoittautuminen(
                    $loggeduser['idhenkilo'],
                    $tapahtuma['idtapahtuma']
                );
            } else {
                $ilmoittautuminen = null;
            }

            echo $templates->render('tapahtuma', [
                'tapahtuma'       => $tapahtuma,
                'ilmoittautuminen'=> $ilmoittautuminen,
                'loggeduser'      => $loggeduser,
            ]);
        } else {
            echo $templates->render('tapahtumanotfound');
        }
        break;

    // Tilin luonnin lomake + käsittely
    case '/lisaa_tili':
        if (isset($_POST['laheta'])) {
            // Siistitään lomakedata
            $formdata = cleanArrayData($_POST);

            require_once CONTROLLER_DIR . 'tili.php';
            $tulos = lisaaTili($formdata, $config['urls']['baseUrl']);

            if ($tulos['status'] === '200') {
                // Tili luotu onnistuneesti
                echo $templates->render('tili_luotu', ['formdata' => $formdata]);
            } else {
                // Lomakkeessa virheitä
                echo $templates->render('lisaa_tili', [
                    'formdata' => $formdata,
                    'error'    => $tulos['error'],
                ]);
            }
        } else {
            // Ensimmäinen kerta lomakkeella
            echo $templates->render('lisaa_tili', [
                'formdata' => [],
                'error'    => [],
            ]);
        }
        break;

    // Kirjautumissivu + kirjautumisen käsittely
    case '/kirjaudu':
        if (isset($_POST['laheta'])) {
            require_once CONTROLLER_DIR . 'kirjaudu.php';

            $email    = $_POST['email']    ?? '';
            $salasana = $_POST['salasana'] ?? '';

            if (tarkistaKirjautuminen($email, $salasana)) {
                require_once MODEL_DIR . 'henkilo.php';
                $rows = haeHenkiloSahkopostilla($email);
                $user = $rows ? array_shift($rows) : null;

                if ($user && $user['vahvistettu']) {
                    // Regeneroidaan session ID turvallisuussyistä
                    session_regenerate_id(true);
                    $_SESSION['user']  = $user['email'];
                    $_SESSION['rooli'] = $user['rooli'];

                    header("Location: " . $config['urls']['baseUrl']);
                    exit;
                } else {
                    // Tili ei ole vielä vahvistettu
                    echo $templates->render('kirjaudu', [
                        'error' => ['virhe' => 'Tili on vahvistamatta! Ole hyvä, ja vahvista tili.'],
                    ]);
                }
            } else {
                // Väärä käyttäjätunnus tai salasana
                echo $templates->render('kirjaudu', [
                    'error' => ['virhe' => 'Väärä käyttäjätunnus tai salasana!'],
                ]);
            }
        } else {
            // Näytetään tyhjä kirjautumislomake
            echo $templates->render('kirjaudu', ['error' => []]);
        }
        break;

    // Uloskirjautuminen
    case '/logout':
        require_once CONTROLLER_DIR . 'kirjaudu.php';
        logout();
        header("Location: " . $config['urls']['baseUrl']);
        exit;

    // Ilmoittautuminen tapahtumaan (uusi ilmoittautuminen)
    case '/ilmoittaudu':
        if (isset($_GET['id']) && $loggeduser) {
            require_once MODEL_DIR . 'ilmoittautuminen.php';

            $idtapahtuma = (int)$_GET['id'];

            $rooli        = $_POST['rooli']        ?? 'kävijä';
            $muistiinpanot= isset($_POST['muistiinpanot'])
                ? trim($_POST['muistiinpanot'])
                : null;

            // Sallitut roolit
            $sallitut = ['esiintyjä', 'myyjä', 'kävijä', 'vapaaehtoinen', 'cosplayer'];
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
            // Jos ei ole kirjautunut tai id puuttuu -> takaisin listaan
            header("Location: tapahtumat");
            exit;
        }
        break;

    // Olemassa olevan ilmoittautumisen päivitys
    case '/paivita_ilmoittautuminen':
        if (isset($_GET['id']) && $loggeduser) {
            require_once MODEL_DIR . 'ilmoittautuminen.php';

            $idtapahtuma = (int)$_GET['id'];

            $rooli        = $_POST['rooli']        ?? 'kävijä';
            $muistiinpanot= isset($_POST['muistiinpanot'])
                ? trim($_POST['muistiinpanot'])
                : null;

            $sallitut = ['esiintyjä', 'myyjä', 'kävijä', 'vapaaehtoinen', 'cosplayer'];
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

    // Ilmoittautumisen peruminen
    case '/peru':
        if (isset($_GET['id'])) {
            require_once MODEL_DIR . 'ilmoittautuminen.php';
            $idtapahtuma = (int)$_GET['id'];

            if ($loggeduser) {
                poistaIlmoittautuminen($loggeduser['idhenkilo'], $idtapahtuma);
            }

            header("Location: tapahtuma?id=$idtapahtuma");
        } else {
            header("Location: tapahtumat");
        }
        exit;

    // Tilin vahvistus sähköpostilinkin avulla
    case '/vahvista':
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
            exit;
        }
        break;

    // Salasanan vaihtolinkin tilaaminen
    case '/tilaa_vaihtoavain':
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
                $tulos = luoVaihtoavain($formdata['email'], $config['urls']['baseUrl']);

                if ($tulos['status'] === '200') {
                    // Vaihtolinkki lähetetty sähköpostiin
                    echo $templates->render('tilaa_vaihtoavain_lahetetty');
                    break;
                }

                // Vaihtolinkin lähetyksessä tapahtui virhe
                echo $templates->render('virhe');
                break;
            } else {
                // Tunnusta ei ollut, mutta näytetään silti sama ilmoitus
                echo $templates->render('tilaa_vaihtoavain_lahetetty');
                break;
            }

        } else {
            // Lomakkeelta ei ole lähetetty tietoa, näytetään lomake
            echo $templates->render('tilaa_vaihtoavain_lomake');
        }
        break;

    // Salasanan resetointi vaihtoavaimella
    case '/reset':
        // Otetaan vaihtoavain talteen
        $resetkey = $_GET['key'] ?? null;

        if (!$resetkey) {
            echo $templates->render('reset_virhe');
            break;
        }

        require_once MODEL_DIR . 'henkilo.php';
        $rivi = tarkistaVaihtoavain($resetkey);

        // Tarkistetaan, että vaihtoavain löytyy ja on voimassa
        if (!$rivi || $rivi['aikaikkuna'] < 0) {
            echo $templates->render('reset_virhe');
            break;
        }

        // Vaihtoavain on voimassa, tarkistetaan onko lomakkeen kautta
        // syötetty tietoa.
        $formdata = cleanArrayData($_POST);

        if (isset($formdata['laheta'])) {
            // Käyttäjä on syöttänyt uudet salasanat
            require_once CONTROLLER_DIR . 'tili.php';
            $tulos = resetoiSalasana($formdata, $resetkey);

            if ($tulos['status'] === '200') {
                // Salasana vaihdettu, näytetään ilmoitus
                echo $templates->render('reset_valmis');
            } else {
                // Näytetään lomake virheviestin kanssa
                echo $templates->render('reset_lomake', [
                    'error' => $tulos['error'],
                ]);
            }
        } else {
            // Lomakkeen tietoja ei ole vielä täytetty, näytetään lomake
            echo $templates->render('reset_lomake', ['error' => '']);
        }
        break;

    // Yksinkertainen infosivu
    case '/info':
        echo $templates->render('info');
        break;

    // Kirjautuneen käyttäjän omat tapahtumat
    case '/omat_tapahtumat':
        // Näkyy vain kirjautuneelle
        if (!$loggeduser) {
            header("Location: " . $config['urls']['baseUrl'] . "kirjaudu");
            exit;
        }

        require_once MODEL_DIR . 'ilmoittautuminen.php';
        $omatTapahtumat = haeIlmoittautumisetKayttajalle($loggeduser['idhenkilo']);

        echo $templates->render('omat_tapahtumat', [
            'tapahtumat' => $omatTapahtumat,
            'loggeduser' => $loggeduser,
        ]);
        break;

    // Oletus: reittiä ei löytynyt
    default:
        // Voit halutessasi tehdä erillisen 404-sivun templates-kansioon
        echo $templates->render('virhe');
        break;
}
