# Provera verzije 3.6.0

**Paket 3.6.0 je postavljen na produkciju i proveren.** Prošle su [provere izdvojene grane](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42388064667) i [main](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42388489304). Svih 165 datoteka proverenog paketa/grane poređeno je sa lokalnim izvorom po Git blob SHA i veličini. Završna dokumentacija ne menja dodatak ili ZIP.

- **33.194 WordPress tvrdnje** u CI-ju, uključujući 23 nove o migraciji palete, sanitizaciji svih devet polja, očuvanju pravila/prilagođenih boja i belim natpisima sa dovoljnim kontrastom.
- **141 JavaScript tvrdnja** i **307 tvrdnji u stvarnom Chromium-u**, lokalno i u CI-ju: 98 atlas, 15 polish, 56 presentation, 57 update34, 50 ranking i 31 nova Sunrise provera. Novi tok uključuje nativno čuvanje boja kroz WP obrazac i stvarnu promenu javnih površina, bez promene igre.
- Lokalno su vizuelno pregledani početna u obe teme, ceo ekran 2560 px, mobilni prikaz i rezultati malih beogradskih opština sa pomerenim brojevima, kao i zastave sa Sunrise okvirom i istom pozadinom ostalih opcija.
- Produkcija: **1.824 javne HTTP tvrdnje**, **61 Chromium tvrdnja**, dve gostujuće partije (okrug i opštine), svi jezici i šest administratorskih prikaza. Administratorski nalog je odjavljen i sesija zatvorena. Nisu dodavani bodovi nalogu za održavanje.

[Pojedinačni elementi i ispravke](PREGLED-3.6.md) · [Produkcijska evidencija i granice provere](LIVE-CHECK.md).

# Prethodna provera verzije 3.5.0

**Konačni paket je 3.5.1 i postavljen je na produkciju.** Ispravka je ograničena na prostor nativnog polja meseca i osvežavanje verzije resursa. Posle nje ponovo su prošli kompletna MySQL/Chromium provera grane i main, 1.824 javne HTTP tvrdnje i 21 nova produkcijska Chromium tvrdnja bez novih partija. Pregledan je najduži naziv meseca na 320 px u četiri jezika. Pre ispravke proverene su još 67 produkcijskih HTTP i 46 Chromium tvrdnji, tri gostujuće partije i jedan stvaran upis bodova tehničkog naloga (70/100), uz nastavak bez dupliranja i skrivanje rezultata sa javne liste. Backend bodovanja nije menjan ispravkom 3.5.1. [Produkcijska evidencija](LIVE-CHECK.md).

Lokalno je prošlo **276 tvrdnji u stvarnom Chromium-u**: prethodnih 226 i 50 novih za kompaktnija pravila, broj iznad ilustracije, beli tekst/kontrast i akcent #FFD447, stvarnu SVG pozadinu, pravilan kružni loader sa slojem preko celog sajta, poteze i navigaciju, dve rang liste, tri perioda, filter igre, nadimak i skrivanje, stvarni obračun 100/100 i dodavanje vežbanja samo u opšti zbir. Nova provera prolazi kroz nativnu prijavu i obrasce, proverava pogrešan nonce, administratorski pregled/CSV i zabranu Pretplatniku. Na 320 px tabela prikazuje partije/prosek ispod nadimka. Pregledani su snimci; fizički telefon nije korišćen.

Novi `tests/ranking.php` prošao je sa **1.278 tvrdnji**: opseg 0–100 i sve težine, pomoć i izvor, zamrznuta pravila, autoritet servera, stvarna runda zastava svih 220 teritorija na četiri jezika, gost/preuzimanje, jedinstveni dnevni slot, ponovljeni potezi/nastavci, oba tipa liste, igre i kalendarske granice, izjednačeni plasman preko granice stranice, privatnost/nadimak, čuvanje nezavisno od analitike, čišćenje samo nezavršenih rezervacija i WP izvoz/brisanje. Obrazac datuma zadržava i obične WP permalinke.

Prošle su postojeće PHP/JS provere igre, naloga, izvora, baze/Beograda i rezervnog transporta, uz **302 lokalne HTTP tvrdnje** za nativne naloge, četiri jezika, šest administratorskih prikaza, podešavanja, CSV/JSON i izolaciju korisnika. Novi testovi ne koriste produkcijske naloge. [Odvojeni pregled svakog elementa i ispravke](PREGLED-3.5.md).

# Provera verzije 3.4.0

Lokalno je prošlo **226 tvrdnji u stvarnom Chromium-u**: prethodnih 169 plus 57 novih provera. Novi `tests/update34-browser.cjs` proverava početnu na četiri jezika, četiri SVG ilustracije pravila pre baze, istaknut CTA sa kontrastom najmanje 4,5:1, beli glavni tekst, mobilni prikaz, unos iznad istorije, izbor/rešavanje gradske opštine, mapu i tabelu svih 17 beogradskih opština, Vračar sa tačnim RZS podacima i MRE izvorom, svih šest promašaja, karticu i marker zadate teritorije, kao i uvećanje zastava pre/posle odgovora na 1440 i 320 px. Uvećanje nije slalo potez niti otkrivalo rešenje, a Escape je vraćao fokus. Provereno je i dobrovoljno otvaranje novog vežbanja iz starije partije, uz prelazak na katalog od 190 teritorija. Kvačica je SVG, kružna i van fotografije. Snimci su pregledani; fizički telefon nije korišćen.

Novi `tests/belgrade.php` prošao je sa **354 tvrdnje**: sve geometrije, svih 17 pojedinačnih RZS brojeva, zbir 1.681.405, nacionalni zbir 6.647.003, 190 aktivnih teritorija bez dupliranja, KiM, četiri runde na četiri jezika, stabilni odgovori, izvori, dopuna starije prilagođene baze bez gubitka izmena, isključivanje opština, očuvana stara istorija i neizmenjena pripremljena dnevna partija. Provereno je prihvatanje starog odgovora `m-beograd` u zamrznutoj ranijoj partiji.

Sve ranije PHP i JavaScript provere ponovo su prošle na proširenoj bazi, uz 301 lokalnu HTTP tvrdnju za nativnu prijavu, naloge, pravila, administraciju, izvoz i privatnost. WordPress ne zahteva dodatni backend ili GIS biblioteke; Shapely/pyproj koriste se samo pri pripremi ugrađenih granica.

Verzija 3.4.0 postavljena je na produkciju. Prošle su 1.334 HTTP tvrdnje i 25 tvrdnji u stvarnom Chromium-u sa uključenom TLS proverom, na širinama 1440 i 320 px. Provereni su početna, pravila, beogradske opštine, Vračar, stvarni potezi, sve četiri dodatne runde i uvećanje zastave pre i posle odgovora. Svih pet administratorskih prikaza radi; sesija je odjavljena. Napravljena su tri gostujuća vežbanja, bez izmene progresa postojećih naloga. Prošle su GitHub Actions provere izdvojene grane i main. [Produkcijska evidencija 3.4.0](LIVE-CHECK.md).

# Provera verzije 3.3.0

Novi `tests/presentation.php` proverava sve ponuđene nazive fotografisanih znamenitosti na četiri jezika, jedinstvene opcije, nepromenjene identifikatore, skrivanje rešenja do odgovora, zvanično ime posle odgovora, sanitizaciju novih polja i ikonicu naloga. Lokalno je prošlo 2.530 tvrdnji.

Novi `tests/presentation-browser.cjs` prošao je sa 56 tvrdnji u stvarnom Chromium-u: unapred popunjena numeracija šest redova, tri promašaja bez promene boje pozadine/oblika i bez ostavljenog busy stanja, odbijen potez bez promene broja pokušaja, poravnanje polja i dugmeta, SVG strelice/ikonice, padajuća lista iznad donjeg reda, račun desno, progres/izvori u istom redu i sva četiri pokušaja na završnoj mapi. Isto je ponovljeno u svetloj/tamnoj temi; odgovori u svim dodatnim rundama ne ostavljaju posvetljenu tablu. Ruski mobilni prikaz je proveren na širini 320 px. Snimci su vizuelno pregledani; fizički telefon nije korišćen.

Uzrok posvetljivanja bio je `aria-busy=true` koji je ostajao posle ponovnog renderovanja. Render sada uvek usklađuje taj atribut sa stvarnim stanjem, a busy stil više ne menja prozirnost cele table. Izvori i autori fotografija ostaju dostupni po ranijim pravilima. Neutralni nazivi primenjuju se pri prikazu, tako da se sadržaj i identifikatori započetih dnevnih partija ne resetuju.

Lokalno i u MySQL GitHub Actions instalaciji prošle su sve backend i JavaScript provere, uključujući 2.530 novih tvrdnji. Ukupno je prošlo 169 Chromium tvrdnji (98 atlas, 15 učitavanje/oporavak, 56 prikaz pokušaja) i 301 lokalna HTTP tvrdnja za prijavu, podešavanja, izvoz i privatnost. [Produkcijska evidencija 3.3.0](LIVE-CHECK.md): 1.074 HTTP tvrdnje i pet administratorskih prikaza.

# Provera verzije 3.2.0

Proverene su tabele opština, kompaktni meni i animacije sa reduced-motion podrškom. Novi testovi `tests/transport.js`, `tests/transport.php` i `tests/polish-browser.cjs` proveravaju rezervni WP AJAX kanal, nonce/vlasništvo, idempotentan početak i odgovor izgubljen posle prihvaćenog poteza. Stvarni browser test se prijavljuje kao izolovani Pretplatnik i potvrđuje jedan upis rezultata. Ispravljeno je i same-origin pravilo prijave u Chrome-u.

Lokalno su prošle 113 provera u Chromium-u (98 atlas/meni i 15 tabela/učitavanje/oporavak), 7.608 atlas tvrdnji na četiri jezika, 17 backend i osam JS provera rezervnog kanala, kao i 301 HTTP provera prijave, podešavanja, izvoza i privatnosti. Tabele se porede sa izvornim brojem stanovnika i godinom za svih 174 opštine/grada u sva četiri jezika. Snimci su pregledani za tabelu sa podacima, KiM bez podataka i prikaz širine 320 px. Fizički telefon nije korišćen.

Na prijavljenom HTTP/2 problemu izvršen je produkcijski spot-check: gostujući `giveup` i `finish` vratili su HTTP/2 200. Povremeni kvar sa snimka nije ponovljen u toj proveri; nova verzija dodaje oporavak zahteva i ne tvrdi da menja HTTP/2 konfiguraciju hostinga.

[Produkcijska evidencija za 3.2.0](LIVE-CHECK.md): 1.009 HTTP tvrdnji, pet administratorskih prikaza i identičan proveren ZIP. GitHub Actions je uspešno ponovio backend i Chromium provere na MySQL-u.

# Provera verzije 3.1.0

[Mapa, promena jezika i teme — tok i provere](ATLAS-3.1.md). Dodato je 6.216 WP tvrdnji za sve okruge/opštine i 98 provera u pravom Chromium-u, uključujući klikove na SVG, tastaturu, dodir, teme i prikaze širine 320, 390, 768 i 1440 px. Fizički telefon nije korišćen. Postojeće provere igre, naloga, analitike i izvoza ponovo se izvršavaju na novom kodu.

# Provera verzije 3.0.0

Verzija 3.0.0 proverena je na izolovanom WordPress-u 7.1.4 sa PHP 8.4.27 i SQLite integracijom kroz WP `wpdb`. Testovi nisu slati produkcijskim korisnicima; testna e-pošta je presretana lokalno.

| Skup | Provera |
|---|---:|
| Postojeći PHP backend, nalozi, resursi i QA regresije | 5.327 |
| Novi jezici, pitanja svih 204 teritorije, pravila, istorija, privatnost i administracija | 13.655 |
| Postojeći JS tok i događaji | 90 |
| Novi JS prevodi, parametri API-ja, pravila i događaji promene teme | 43 |
| Novi HTTP test četiri jezika, WP prijave, admin formi i izvoza | 301 |
| Raniji puni HTTP tok, ponovo izvršen na 3.0.0 | 271 |

Ukupno **19.687 tvrdnji u testovima**, uz PHP/JS sintaksu i proveru determinističkog instalacionog ZIP-a. Broj tvrdnji nije broj uređaja, korisnika ili nezavisnih scenarija.

Novi HTTP test proverava svih pet javnih pogleda u četiri jezika, privatni nalog, obris i opštine okruga, pitanja i pregled odgovora, promenu jezika između dve sesije istog naloga, zabranu drugom nalogu, administratorske stranice, čuvanje pravila uz stvarni nonce, odbijanje pogrešnog nonce-a, svih šest CSV/JSON preuzimanja i zabranu njihovog preuzimanja Pretplatniku, registraciju i postavljanje lozinke na ruskom. PHP provere pokrivaju stabilne identifikatore odgovora u svim jezicima, zamrzavanje pravila, uvoz starijih zbirnih zapisa, izbegavanje duplog beleženja, spajanje gosta sa nalogom, HMAC identifikatore, zaštitu CSV formula i WP izvoz/brisanje podataka sa poništavanjem starih sesija.

Svetla/tamna/automatska tema proverene su kroz generisani HTML, CSS pravila i JavaScript događaje/pamćenje izbora. **Vizuelni pregled u pravom pregledaču i na fizičkom telefonu nije izvršen.** Lokalna dostava pošte je simulirana; produkcijska SMTP isporuka nije deo ovih testova. GitHub Actions dodatno pokreće PHP testove na MySQL-u sa Autoptimize i Slim SEO.

Ponovljivi novi testovi: `tests/v3-features.php`, `tests/v3-frontend.js`, `tests/http-v3.py`. HTTP harness zahteva beautifulsoup4, promenljive `WP_ROOT`, `PHP_BIN` (opciono), `SND_TEST_URL` i lokalni backend server, npr. `WP_ROOT=/putanja/wp SND_TEST_URL=http://127.0.0.1:8899 php -S 127.0.0.1:8899 -t /putanja/wp tests/http-router.php`. Testna WP konfiguracija mora koristiti isti `SND_TEST_URL` za `WP_HOME` i `WP_SITEURL`. Harness odbija servere koji nisu loopback; fixture odbija produkcijsko okruženje. Testovi menjaju namenske podatke i ne pokreću se na produkciji.

# Provera verzije 2.1.0 (prethodna evidencija)

Ispravke dva naknadna QA izveštaja, sa 5.419 provera na izdvojenom WordPress-u i granicama verifikacije, opisane su u [QA-ISPRAVKE.md](QA-ISPRAVKE.md). Novi paket 2.1.0 treba postaviti na produkciju. Ispod ostaje prethodna evidencija za 2.0.0.

# Provera — Srbija na dlanu 2.0.0

Izvršeno 6. novembra 2026. na izolovanom WordPress-u 7.1.4, PHP-u 8.4.27 i SQLite bazi kroz standardni WP `wpdb` interfejs. Testna WP instalacija i presretanje testne pošte nisu deo instalacionog ZIP-a.

Ukupno **3.290 provera**:

| Skup | Broj | Obuhvat |
|---|---:|---|
| Backend i podaci | 2.837 | Svih 204 teritorije, četiri bonus pitanja po teritoriji, jedinstveni izbori, popisni zbir, API tok, nevažeći/ponovljeni potezi, revizije, pomoć, dnevna nepromenljivost, nastavak i uvoz |
| Nalozi, privatnost, zastave i zaključavanje | 148 | Vlasništvo partije, uskraćen pristup gostu/drugom nalogu, serverski skor, upis jednom, preuzimanje gostujuće partije, neponovljiv dnevni pokušaj, važeće lokalne zastave sa izvorima, oporavak isteklog zaključavanja i zaštita novog zaključavanja od stare operacije |
| Učitavanje resursa | 10 | Elementor metapodaci, prethodno odbačen render, kasni CSS, bez dupliranja i bez resursa igre na drugim stranicama |
| JavaScript tok i HTML | 24 | Provera WP sesije pre početka, nonce, odvojeni ključevi gosta/naloga, vezivanje partije, fiksna igra po stranici, slika iznad pitanja, četiri slike zastava, zaključani odgovori i serverska statistika |
| HTTP i WP kolačići | 271 | Šest app stranica, svih 30 detalja okruga, samo silueta okruga, ćirilična pretraga, očuvana tema van aplikacije, nativna registracija/prijava/odjava, nonce greške, postavljanje i promena lozinke, iskorišćen link, privatni keš, preuzimanje partije i drugi uređaj |

Brojevi iz izveštaja predstavljaju tvrdnje u testovima, ne broj različitih uređaja ili pregledača. HTTP test je koristio lokalni backend server i stvarni WordPress sa kolačićima; nije korišćen pregledač. Testna pošta je presretana unutar izolovane instalacije i nije slata stvarnim primaocima. Registracija je proverena sa ulogom Pretplatnik, a reset link vodi na aplikaciju i zatim se uklanja iz URL-a u HTTP-only kolačić.

Svih 30 okružnih celina i 174 opštine/grada ostaju u bazi. Kosovo i Metohija: pet okruga i 29 opština, bez izmišljenog Popisa RZS 2022. Zbir ostalih 145 opština/gradova je 6.647.003. Paket sadrži 30 fotografija i 52 slike zastava; zastave pokrivaju 69 teritorija, uz jasno zamensko pitanje za ostale. Slike zastava pregledane su na kontaktnom listu i imaju pojedinačne izvore/licence u FLAGS.md.

PHP i JavaScript sintaksa proverene. ZIP se pravi deterministički, ima jedan glavni direktorijum dodatka i proverava se prema svim izvornim datotekama. U paketu nema razvojnih zavisnosti, testne WP instalacije, naloga ili pristupnih podataka.

## Granice provere i produkcija

**Vizuelna provera u pravom pregledaču nije izvršena**, jer alat za pregledač nije dostupan. Provereni su generisani HTML, redosled slika/pitanje, ponašanje JavaScript zahteva i responzivna CSS pravila; to ne potvrđuje konačan prikaz na telefonu ili konkretnoj kombinaciji Astra/Elementor/Autoptimize.

Verzija **2.0.0 je postavljena na cvjetan.dev**, nakon što je vlasnik ručno otpremio ZIP. Automatsko otpremanje kroz prvobitnu administratorsku prijavu zaustavljao je DreamWeb; njegova provera nije zaobilažena. Posle postavljanja prošle su **134 HTTP provere na produkciji**: šest WP stranica, svih 30 detalja okruga sa zasebnim obrisima, ćirilična pretraga, lokalni/optimizovani resursi, obe igre sa četiri dodatna pitanja, slike zastava i nastavak gostujuće partije.

Nativna prijava preko nove stranice aplikacije proverena je na privremenom nalogu: preuzimanje gostujućeg vežbanja, upis jednog rezultata, bez dupliranja pri ponovnom otvaranju, prikaz na nalogu, isti progres u drugoj nezavisnoj sesiji, zabrana pristupa privatnoj partiji gostu i odjava. Privatne stranice i AJAX sesija vraćaju no-store. Zaglavlje/podnožje Astra teme odsutni su na aplikaciji i ostaju na glavnom sajtu. Autoptimize isporučuje stilove aplikacije inline i optimizovan JavaScript preko svog PHP resursa; sadržaj je potvrđen. Test je dodao jedno završeno vežbanje privremenom nalogu. **Vizuelni prikaz u pravom pregledaču i produkcijska dostava WP e-pošte nisu testirani.**

Ponovljive PHP/JS provere su u `tests/`; pokretati ih samo na namenskoj lokalnoj/razvojnoj WP instalaciji. HTTP testni harness je korišćen u izolovanom radnom okruženju. Ograničenja i licence geografskih izvora opisani su u DATA-LICENSE.md.
