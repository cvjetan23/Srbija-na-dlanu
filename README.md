# Srbija na dlanu 4.0.0

Zasebna geografska aplikacija unutar WordPress-a: pet dnevnih igara, baza znanja i nalozi igrača. Sveden prikaz: slika gore, pitanje i odgovori ispod, sa većim slovima.

**[Preuzmi instalacioni ZIP](https://github.com/cvjetan23/Srbija-na-dlanu/raw/refs/heads/main/dist/srbija-na-dlanu.zip)** · [Instalacija](srbija-na-dlanu/INSTALACIJA.md) · [Provere](docs/PROVERA.md) · [Zastave i licence](srbija-na-dlanu/FLAGS.md)

**Izdanje 4.0.0:** pet igara sa jednim zadatkom po danu i zajedničkom rang listom. [Igre i pravila](docs/NOVE-IGRE.md) · [Podaci i fotografije](srbija-na-dlanu/EXPLORE-SOURCES.md) · [Produkcijska provera](docs/LIVE-CHECK.md).

## Instalacija i ažuriranje

1. Preuzmi `dist/srbija-na-dlanu.zip`. GitHub **Code → Download ZIP** preuzima ceo repo i nije instalacioni paket dodatka.
2. WordPress → **Dodaci → Dodaj novi → Otpremi dodatak**. Pri ažuriranju izaberi **Zameni postojeći dodatak**. Aktiviraj ako je potrebno.
3. Otvori WP administraciju: dodatak automatski priprema stranice. Otvori `/srbija-na-dlanu/`.
4. Očisti Autoptimize, keš stranica i CDN. Proveri dostavu WP poruka za postavljanje i promenu lozinke.

| Stranica | Putanja |
|---|---|
| Početna | `/srbija-na-dlanu/` |
| Izbor svih pet igara | `/srbija-na-dlanu/igre/` |
| Gradovi i naselja | `/srbija-na-dlanu/gradovi/` |
| Reke | `/srbija-na-dlanu/reke/` |
| Planine | `/srbija-na-dlanu/planine/` |
| Okruzi | `/srbija-na-dlanu/okruzi/` |
| Opštine i gradovi | `/srbija-na-dlanu/opstine/` |
| Baza znanja | `/srbija-na-dlanu/baza-znanja/` |
| Rang lista | `/srbija-na-dlanu/rang-lista/` |
| Prijava / registracija / lozinka | `/srbija-na-dlanu/prijava/` |
| Moj nalog | `/srbija-na-dlanu/nalog/` |

Ove stranice imaju svoj kompas, meni, zaglavlje i podnožje, bez zaglavlja i podnožja aktivne teme. Ostali deo sajta zadržava postojeći izgled. Postojeći Elementor podaci početne stranice se ne brišu. Dodatak ne menja globalnu početnu stranicu WordPress-a.

Shortcode `[srbija_na_dlanu]` i dalje radi na drugim WP stranicama; tu se zadržava njihova tema. Za određenu igru koristi `[srbija_na_dlanu kind="district"]` ili `kind="municipality"`, `kind="city"`, `kind="river"`, `kind="mountain"`.

## Novo u 4.0.0

- Aktivne dnevne igre za naselja, reke i planine sa stvarnim mapama, šest pokušaja i foto-pitanjem. Okruzi i opštine zadržavaju zastave.
- Jedan rezultat po nalogu, igri i danu. Do 100 poena po igri i 500 ukupno. Dnevni, mesečni i ukupni plasman sabiraju samo dnevne zadatke nove sezone.
- Baza znanja i WP → Srbija na dlanu → Geografski katalozi imaju naselja, reke i planine; svaki podatak ima izvor.
- Nova sezona čuva stare partije i istoriju. Vežbanje je isključeno u javnom dnevnom režimu; urednik može posebno uključiti prethodni režim u podešavanjima.

## Novo u 3.8.1

- Kompaktnije ikonice teme i izbor jezika. Na mobilnom/tabletu jezik je levo, tema desno, a navigacija počinje od leve ivice.
- [Razrađen predlog tri nove dnevne igre i zajedničkog rangiranja](docs/NOVE-IGRE.md). Ovo je koncept za pregled, a ne objava novih igara.

## Novo u 3.8.0

- Glavne akcije, formulari, izbor režima igre i filteri rang liste dele isti izgled: zelena podloga, beli tekst, 18 px font, najmanje 50 px visina i 12 px radijus. Aktivni filter ima okvir umesto zasebne donje trake.
- Tačan odgovor je zelen sa belim tekstom, a izabrani netačan Sunrise sa tamnim borovim tekstom. Kvačica i iks ostaju uz odgovor. Isto važi za tekstualna pitanja i zastave u obe teme; slike se ne prebojavaju.
- Lupica je na slici zastave, bez vidljivog natpisa. Ima pristupačan naziv, zasebno otvara pregled i ne potvrđuje odgovor. Escape i zatvaranje vraćaju fokus.

## Novo u 3.7.0

- **Šuma i sunce**: nova samostalna paleta inspirisana prirodom, sa borovom i žad zelenom, toplom svetlom podlogom i Sunrise akcentom. [Istraživanje, HEX vrednosti i kontrast](docs/PALETA-3.7.md).
- Sedam osnovnih boja sa jasnim ulogama: mirna podloga, odvojene površine, čitljiv tekst i istaknute akcije. Dugmad imaju bele natpise sa kontrastom 6,39:1; Sunrise je mali, prepoznatljiv detalj.
- Diskretnije reke, blaže ivice kartica i jače granice interaktivnih polja. Ispravni odgovori zadržavaju svoju površinu.
- **WP → Srbija na dlanu → Podešavanja → Vrati predložene boje** obnavlja samo paletu. Pravila, jezici, registracija i bodovi se ne resetuju. Prilagođene palete se čuvaju kao celina.

## Novo u 3.6.0

- Paleta je preuzeta sa glavnog sajta: maslinasta `#6F7C45`, tamna `#171914`, površina `#292C25`, svetla `#EEF0E5`, bela i ivica `#D8DCCB`, uz **Sunrise `#FFD447`**. Sedam osnovnih boja zamenjuje ranije mešanje zelenih i zlatnih nijansi.
- Ravna dugmad bez donje trake, beli natpisi na maslinastoj i Sunrise akcenti u tamnoj temi. Strelice pokušaja su Sunrise. Tačan odgovor ima Sunrise okvir uz istu pozadinu kao ostali odgovori.
- Brojevi pokušaja i zvezdica na mapi su manji. Na malim opštinama oznake su pomerene van oblika i povezane linijom; pri promeni širine ostaju iste veličine u pikselima.
- Reke su diskretna, obojiva SVG maska preko celog ekrana, uključujući velike širine i skrolovanje.
- **WP → Srbija na dlanu → Podešavanja → Paleta boja**: sedam osnovnih boja i dva dodatna izbora za tamnu temu. Ranije podrazumevane boje prelaze na novu paletu, dok se namerno prilagođene vrednosti čuvaju. Ne menjaju se runde, progres ili bodovi.

## Novo u 3.5.0

- Bodovanje svake nove partije od 0 do 100. Podrazumevano: do 60 za teritoriju, −6 za dodatni pokušaj, do 40 za kviz, −10 za pomoć pri pogađanju. Odgovor uz pomoć izvora ne donosi poene za to pitanje.
- **Rang lista** ima dve odvojene celine: **Dnevni izazovi** (jedan rezultat po igri i danu) i **Opšta rang lista** (izazovi i sva vežbanja). Obe imaju dnevni, mesečni i ukupni pregled, za sve igre ili odvojeno za okruge/opštine. Poeni na listi su zbir; svaka pojedinačna partija ima najviše 100.
- Za plasman je potrebna prijava pre početka partije. Gost vidi bodove i zadržava progres pri prijavi, ali se rezultat ne rangira naknadno. Ranije partije se ne preračunavaju. Nadimak je podrazumevano pseudonim; na nalogu može da se izmeni ili sakrije javni plasman.
- Bodovi se čuvaju nezavisno od analitike, bez njenog roka brisanja. U WP administraciji postoje pregled, podešavanje bodovanja i CSV izvoz. Brisanje/izvoz ličnih podataka uključuje rang listu.
- Brojevi pravila su na ilustracijama, a kartice prate dužinu teksta. Loader je pravilan krug sa pozadinom, preko zatamnjenog sloja na celoj stranici.
- Tamna tema koristi bela slova na tamnozelenim dugmadima i male akcente **#FFD447**. Diskretna pozadina ima stvarne linije Dunava, Save, Drine, Morave i Tise iz javnih Natural Earth podataka; može se isključiti.
- Podrazumevana runda zastava ostaje za svaku teritoriju: ako lokalna nije potvrđena, pita se zastava sedišta ili drugog jasno imenovanog mesta. Ne pripisuje se tuđa zastava opštini. Postojeće aktivne partije zadržavaju pitanja; nove partije dobijaju obnovljenu rundu.

## Novo u 3.4.0

- Početna ima istaknuta dugmad Igraj i četiri ilustrovana koraka, pre baze znanja. Tamna tema koristi belo za glavni tekst i svetlozelenu za sporedni.
- Unos odgovora je iznad spiska pokušaja. Zadati okrug/opština ima izdvojen naziv, jaču granicu i posebnu oznaku na mapi ako nije pogođen.
- Svaka zastava ima zasebno uvećanje koje ne potvrđuje odgovor. SVG oznake tačnog/netačnog odgovora stoje uz naziv, bez preklapanja slike.
- Dodato je 17 beogradskih gradskih opština sa stvarnim granicama i Popisom RZS 2022. Aktivna igra opština ima **190 teritorija**: dosadašnji ceo Grad Beograd zamenjen je sa 17 opština; zbir stanovništva nije dupliran. Grad Beograd ostaje posebna celina u igri okruga.
- Započete/dnevne partije i stara istorija ostaju sačuvani. Nove opštine ulaze u nove partije. Istorijski zapis `m-beograd` ostaje u bazi, a stari link ka njemu vodi na detalj Beograda sa 17 opština.

## Novo u 3.3.0

- Svih šest polja za pokušaje vidi se pre prvog odgovora, sa brojevima i istaknutim sledećim poljem; broj prati podešavanje administratora.
- Završna mapa prikazuje sve pokušaje, njihove brojeve i odgovarajuću legendu. Strelice su SVG ikonice.
- Ispravljen je zaostali `aria-busy` posle poteza i uklonjeno posvetljivanje cele table. Unos i dugme imaju jednaku visinu.
- Nalog/prijava je zasebna ikonica sasvim desno; progres i izvori dele donji red. Dodate su ikonice pomoći i drugih akcija.
- Ponuđeni nazivi znamenitosti ne sadrže nazive gradova koji otkrivaju odgovor. Puni nazivi ostaju u bazi i objašnjenjima. Neutralni nazivi za sva četiri jezika mogu se menjati uz teritoriju u administraciji.

## Novo u 3.2.0

- Opštine ispod mapa prikazuju se u tabeli: naziv, stanovništvo i godina popisa. Nepostojeći podaci jasno su označeni.
- Jezik je ravan deo zaglavlja bez okvira i pozadine; uklonjene su vizuelne oznake Jezik/Tema. Ikonice zadržavaju pristupačna imena.
- Animirani kompas označava učitavanje igre, poteza i prelazak na drugu stranicu. Dodati su blagi prelazi teksta/teme, sa poštovanjem reduced-motion podešavanja.
- Kod mrežnog prekida REST zahteva igra koristi rezervni WP AJAX kanal, iste provere vlasništva i revizija i oporavak bez ponavljanja prihvaćenog poteza.
- Stvarna prijava kroz pregledač i gubitak odgovora posle prihvaćenog poteza pokriveni su dodatnim Chromium testom.

## Novo u 3.1.0

[Mape i provere prikaza](docs/ATLAS-3.1.md).

- Jasno označen izbor jezika sa punim nazivima i trenutnim izborom.
- Sunce, mesec i automatski izbor teme, uz pamćenje i pristupačne oznake.
- Baza znanja počinje interaktivnom mapom Srbije sa svih 30 okružnih celina.
- Klik na okrug otvara njegovu mapu sa opštinama; klik na opštinu otvara obris, podatke i istaknut položaj u okrugu.
- Spiskovi svih okruga i pripadajućih opština ostaju ispod mapa. Mape i izbor jezika koriste obične linkove i rade i bez JavaScript-a.
- Sačuvani su svih 174 opštine/grada, Kosovo i Metohija i postojeći progres.

## Novo u 3.0.0

- Svetla, tamna i automatska tema; izbor se pamti na uređaju.
- Srpski, engleski, nemački i ruski: meni, igre, pitanja, objašnjenja, baza znanja, prijava, poruke za lozinku i deljenje. Zvanični nazivi većine opština ostaju na srpskoj latinici.
- WP podmeniji **Analitika**, **Igrači**, **Partije**, **Podešavanja** i **Prevodi**. Broj pokušaja, pomoć, runde, jezici, tema, boje, veličina teksta, registracija i analitika podešavaju se u administraciji.
- Istorija pojedinačnih partija: ko je igrao, teritorija, pokušaji, odgovori u kvizu, pomoć, ishod i vreme. Filteri, dnevni pregled i CSV/JSON izvoz.
- Postojeći dnevni rezultati uvoze se kao zbirni zapisi. Detalji starih pokušaja i pojedinačne stare partije vežbanja nisu postojali i ne izmišljaju se.

[Uputstvo za administraciju i analitiku](docs/ADMINISTRACIJA.md). Struktura geografskih podataka i Kosovo i Metohija ostaju sačuvani.

## Ispravke 2.1.0

Ispravke dva QA izveštaja obuhvataju deljene linkove sa režimom/datumom, tastaturu i dodir u pretrazi, pregled pitanja i tačnog odgovora sa slikom zastave, jasan ishod i niz igranja, oporavak slika i sesije, prelazak ponoći, regionalnu pomoć, izvore i uverljivije ponuđene odgovore. Dodatno su smanjeni odgovori API-ja i nepotrebni resursi teme, isključena problematična minifikacija resursa igre i uređeni kontrast i SEO podaci.

[Detaljno: nalaz → ispravka → provera](docs/QA-ISPRAVKE.md). Postojeći podaci, 30 okružnih celina, 174 opštine/grada i Kosovo i Metohija ostaju sačuvani. Banka od 30 fotografija i 52 zastave nije proširivana ovom ispravkom; uputstvo i oznake sada dosledno opisuju geografske zamene.

**Ovaj repozitorijum sadrži paket 3.5.1.** Novi kod i paket proveravaju se u izdvojenoj GitHub Actions instalaciji sa Autoptimize i Slim SEO dodacima. Izvršen je pregled i funkcionalna provera u Chromium-u, sa prikazima širine 320, 390, 768 i 1440 piksela; fizički telefon nije korišćen. Ispravka 3.5.1 sprečava odsecanje dugog naziva meseca uz kalendar na uskom ekranu.

## Sadržaj i igra

- **30 teritorija:** 29 upravnih okruga i Grad Beograd kao posebna celina.
- **190 opština, gradova i gradskih opština**, sa 17 gradskih opština Beograda i Kosovom i Metohijom.
- Stvarne siluete, podrazumevano šest pokušaja (podesivo), udaljenost i strelica od odgovora ka traženoj teritoriji.
- Posle otkrivanja podrazumevano slede **četiri dodatna pitanja** (runde su podesive): stanovništvo/položaj, administrativna geografija, zastava i znamenitost/prostorni izazov.
- **52 lokalne zastave** pokrivaju 17 okružnih sedišta i 51 opštinu/grad: ukupno 68 od 220 aktivnih teritorija. Za ostala mesta podrazumevano se pita potvrđena zastava sedišta ili drugog jasno imenovanog mesta, uz objašnjenje zamene. Zastava okružnog sedišta nije predstavljena kao zastava okruga.
- **30 lokalnih fotografija znamenitosti**, sa autorima, licencama i izvorima.
- Isti dnevni zadatak za sve, promena u ponoć po vremenu u Srbiji; nasumično vežbanje, nastavak i deljenje rezultata bez rešenja.
- Pretraga prihvata latinicu, ćirilicu i unos bez kvačica.
- Baza znanja: mapa Srbije → okrug sa opštinama → detalj opštine, pretraga i spiskovi ispod mapa.

Kosovo i Metohija su uključeni sa **pet okruga i 29 opština** prema podeli korišćenoj u projektu. Popis RZS 2022. tamo nije sproveden; broj stanovnika se ne izmišlja, a pitanje se zamenjuje geografskim. Na ostaloj teritoriji broj stanovnika odnosi se na cele opštine/gradove; zbir je 6.647.003.

## Nalozi i progres

Prijava, odjava i promena lozinke koriste standardne WordPress funkcije i postojeće WP korisnike. Registracija igre pravi samo **Pretplatnika**, bez obzira na globalnu podrazumevanu ulogu; može se isključiti u meniju dodatka. Lozinka se postavlja preko linka u WP e-pošti.

Prijavljenom igraču server čuva aktivnu partiju, upoznate teritorije, dnevne rezultate, vežbanja, kviz i niz dana u WP korisničkim podacima. Drugi uređaj sa istim nalogom nastavlja istu partiju. Dnevna partija se ne resetuje ponovnim otvaranjem. Rezultat se računa na serveru i upisuje jednom. Token privatne partije nije dovoljan drugom korisniku da je otvori.

Gost koristi lokalnu statistiku pregledača i serversku partiju do tri dana. Posle prijave trenutna gostujuća partija može se vezati za nalog, osim kada nalog već ima aktivnu partiju tog tipa. Stara lokalna statistika se ne uvozi kao verifikovani rezultat. Aktivne partije naloga ističu posle 30 dana; ukupni rezultati ostaju na nalogu. Keš može ranije ukloniti WP transient; sačuvani rezultati time se ne brišu.

## Administracija i tehnika

WP meni **Srbija na dlanu** uređuje teritorije, fotografije i potvrđene zastave sa izvorima/licencama; podržan je JSON uvoz/izvoz. Današnji zadatak ostaje nepromenjen posle izmene sadržaja. Deaktivacija ne briše podatke.

WordPress **6.2+**, PHP **7.4+**, nativni JavaScript/CSS. Koriste se WP options, transients, usermeta, REST API, korisnici, nonces i Media Library, uz dve tabele analitike u istoj WP bazi (`{prefix}snd_games`, `{prefix}snd_events`). Nema novog servera, posebne baze, npm instalacije ni API ključeva.

Izuzmi iz keširanja svih šest stranica aplikacije (jezik i tema zavise od izbora posetioca), `/wp-json/srbija-na-dlanu/v1/*` i `admin-ajax.php?action=snd_session`. Dozvoli JSON POST i `X-WP-Nonce`. Dodatak šalje `no-store` za privatne podatke; `snd_session` proverava stvarni WP kolačić i daje svež REST nonce.

## Izrada i provere

```sh
python3 tools/build-package.py
node tests/frontend.js
node tests/qa-frontend.js
node tests/v3-frontend.js
WP_ROOT=/putanja/do/izolovanog/wp php tests/game-flow.php
WP_ROOT=/putanja/do/izolovanog/wp php tests/account-progress.php
WP_ROOT=/putanja/do/izolovanog/wp php tests/asset-loading.php
WP_ROOT=/putanja/do/izolovanog/wp php tests/qa-regressions.php
WP_ROOT=/putanja/do/izolovanog/wp php tests/v3-features.php
WP_ROOT=/putanja/do/izolovanog/wp php tests/atlas-flow.php
```

PHP testove pokreći samo na namenskoj lokalnoj/razvojnoj WP instalaciji sa aktivnim dodatkom. Testovi kreiraju podatke; nisu namenjeni produkciji. [Izveštaj](docs/PROVERA.md) razlikuje HTTP/HTML provere od vizuelnog testa u pregledaču. Za lokalni HTTP test dostupni su `tests/http-router.php`, `tests/http-fixture.php` i `tests/http-v3.py` (beautifulsoup4, loopback server i namenski WP sa lokalnim okruženjem). Prikaz i ponašanje 3.1.0 provereni su u Chromium-u; fizički telefon i produkcijska isporuka e-pošte nisu testirani.

Kod je GPL-2.0-or-later. Geometrija, fotografije i zastave imaju zasebne uslove: [SOURCES.md](srbija-na-dlanu/SOURCES.md), [DATA-LICENSE.md](srbija-na-dlanu/DATA-LICENSE.md), [FLAGS.md](srbija-na-dlanu/FLAGS.md). Za dopunske granice Kosova i Metohije javni servis nije naveo posebnu licencu za redistribuciju. Karte služe igri i nisu katastarske karte.
