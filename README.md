# EventHub – Tapahtumien ilmoittautumisjärjestelmä

EventHub on PHP-pohjainen tapahtumahallintasovellus, jonka avulla käyttäjät voivat
- selata tapahtumia,
- nähdä tapahtumien tarkemmat tiedot,
- ilmoittautua tapahtumiin eri rooleissa,
- päivittää tai perua omia ilmoittautumisia,
- hallita käyttäjätiliään (rekisteröinti, kirjautuminen, salasanan resetointi).

Sovellus sisältää myös admin-näkymän käyttäjähallintaan.

## 🎯 Projektin tarkoitus

Tämä projekti on harjoitustyö, jonka tavoitteena oli:
- toteuttaa MVC-rakenteinen PHP-sovellus ilman frameworkeja
- hyödyntää templaten moottorina *Plates*
- käyttää MySQL/MariaDB-tietokantaa
- rakentaa moderni, responsiivinen ja yhtenäinen käyttöliittymä
- harjoitella käyttäjätilien hallintaa ja turvallisia kirjautumistoimintoja

## 🧱 Teknologiat

- **PHP 8+**
- **Plates templating engine**
- **MySQL / MariaDB**
- **HTML5, CSS3 (custom UI)**
- **Vanilla JavaScript** (vain pieniä toimintoja)
- **Composer** (autoload)

## 📁 Projektin rakenne
<img width="665" height="557" alt="image" src="https://github.com/user-attachments/assets/f2d1a0d1-8dfa-4c64-8a15-7ec1578258d9" />


## 🔒 Käyttäjätilit & kirjautuminen

Sovellus tukee mm.:

- Tilin luonti ja sähköpostivahvistus
- Kirjautuminen ja uloskirjautuminen
- Salasanan resetointi vaihtolinkin avulla
- Admin-näkymä, jossa ylläpitäjä näkee kaikki käyttäjät

Roolit:
- `käyttäjä`
- `admin`

## 🧾 Tapahtumat ja ilmoittautumiset

Käyttäjä voi:

- selata tapahtumia (lajittelu nimen, paikkakunnan ja ajankohdan mukaan)
- avata tapahtuman oman sivun
- ilmoittautua roolissa:
  - kävijä  
  - esiintyjä  
  - myyjä  
  - vapaaehtoinen  
  - cosplayer
- muokata ilmoittautumistaan
- perua ilmoittautumisen
- nähdä omat tapahtumansa erillisellä sivulla

Admin voi nähdä kaikki käyttäjät ja heidän perustietonsa.

## 🎨 Käyttöliittymä

UI on rakennettu täysin custom CSS:llä.  
Tavoitteena oli moderni ja värikäs kokonaisuus:

- responsiivinen layout
- selkeät lajittelupainikkeet
- yhtenäinen tyylipohja kaikilla sivuilla
- gradient-header, “kortti”-main layout ja mobiilinavigointi

## ⚙️ Asennus

1. Kloonaa repo:

```bash
git clone https://github.com/Pet-etine/eventhub

2. Asenna Composer-riippuvuudet:

composer install


3. Luo tietokanta ja aja mukana tulevat SQL-skriptit (löytyvät hakemistosta /sql jos lisäät sellaisen).

4. Aseta yhteydet src/config/database.php -tiedostoon.

5. Käynnistä PHP:n omalla serverillä:

php -S localhost:8000 -t public

Avaa selaimella
👉 http://localhost:8000
Testaaminen

Voit testata sovellusta seuraavasti:

Luo käyttäjätili

Vahvista sähköpostilinkki

Kirjaudu sisään

Ilmoittaudu tapahtumiin

Testaa roolinvaihdot ja muokkaukset

Testaa salasanan resetointi

Kirjaudu ulos ja takaisin

Jos käytettävissä admin-tili → kokeile admin-näkymää

📜 Lisenssi

Projekti on julkaistu Creative Commons Zero (CC0) -lisenssillä.
Voit käyttää, muokata ja jakaa vapaasti.

👤 Tekijä

Petri Venäläinen-Kuusela
(2025) Hyödynnetty ChatGPT, koodin korjaamiseen ja ohjeistukseen.

🙌 Pohja / Credits

Tämän README.md:n rakenne ja projektikuvaus pohjautuvat
LAB-ammattikorkeakoulun kurssimateriaaliin ja opettaja Pekka Tapion
(taloudenhallinta-sovellus) esimerkkipohjaan.

GitHub:
👉 https://github.com/pekkatapio
Kiitos selkeästä ja toimivasta referenssipohjasta!
