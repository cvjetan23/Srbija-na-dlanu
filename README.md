# Srbija na dlanu 3.2

Zasebna geografska aplikacija unutar WordPress-a: početna, igre za okruge i opštine, baza znanja i nalozi igrača. Sveden prikaz: slika gore, pitanje i odgovori ispod, sa većim slovima.

**[Preuzmi instalacioni ZIP](https://github.com/cvjetan23/Srbija-na-dlanu/raw/refs/heads/main/dist/srbija-na-dlanu.zip)** · [Instalacija](srbija-na-dlanu/INSTALACIJA.md) · [Provere](docs/PROVERA.md) · [Zastave i licence](srbija-na-dlanu/FLAGS.md)

**Verzija 3.2.0 postavljena je i aktivna na [cvjetan.dev/srbija-na-dlanu](https://cvjetan.dev/srbija-na-dlanu/).** Prošlo je 1.009 produkcijskih HTTP tvrdnji i pregled svih pet administratorskih stranica. Lokalno i u CI-ju prošlo je 113 Chromium provera mapa, tabele, jezika, teme, učitavanja i oporavka veze. [Evidencija i granice provere](docs/LIVE-CHECK.md).

## Instalacija i ažuriranje

1. Preuzmi `dist/srbija-na-dlanu.zip`. GitHub **Code → Download ZIP** preuzima ceo repo i nije instalacioni paket dodatka.
2. WordPress → **Dodaci → Dodaj novi → Otpremi dodatak**. Pri ažuriranju izaberi **Zameni postojeći dodatak**. Aktiviraj ako je potrebno.
3. Otvori WP administraciju: dodatak automatski priprema stranice. Otvori `/srbija-na-dlanu/`.
4. Očisti Autoptimize, keš stranica i CDN. Proveri dostavu WP poruka za postavljanje i promenu lozinke.

| Stranica | Putanja |
|---|---|
| Početna | `/srbija-na-dlanu/` |
| Okruzi | `/srbija-na-dlanu/okruzi/` |
| Opštine i gradovi | `/srbija-na-dlanu/opstine/` |
| Baza znanja | `/srbija-na-dlanu/baza-znanja/` |
| Prijava / registracija / lozinka | `/srbija-na-dlanu/prijava/` |
| Moj nalog | `/srbija-na-dlanu/nalog/` |

Ove stranice imaju svoj kompas, meni, zaglavlje i podnožje, bez zaglavlja i podnožja aktivne teme. Ostali deo sajta zadržava postojeći izgled. Postojeći Elementor podaci početne stranice se ne brišu. Dodatak ne menja globalnu početnu stranicu WordPress-a.

Shortcode `[srbija_na_dlanu]` i dalje radi na drugim WP stranicama; tu se zadržava njihova tema. Za određenu igru koristi `[srbija_na_dlanu kind="district"]` ili `kind="municipality"`.

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

**Ovaj repozitorijum sadrži paket 3.2.0.** Novi kod i paket proveravaju se u izdvojenoj GitHub Actions instalaciji sa Autoptimize i Slim SEO dodacima. Za 3.2.0 izvršen je pregled i funkcionalna provera u Chromium-u, sa prikazima širine 320, 390, 768 i 1440 piksela; fizički telefon nije korišćen.

## Sadržaj i igra

- **30 teritorija:** 29 upravnih okruga i Grad Beograd kao posebna celina.
- **174 opštine i grada**, sa Kosovom i Metohijom, bez zasebnih gradskih opština.
- Stvarne siluete, podrazumevano šest pokušaja (podesivo), udaljenost i strelica od odgovora ka traženoj teritoriji.
- Posle otkrivanja podrazumevano slede **četiri dodatna pitanja** (runde su podesive): stanovništvo/položaj, administrativna geografija, zastava i znamenitost/prostorni izazov.
- **52 lokalne zastave** pokrivaju 17 okružnih sedišta i 52 opštine/grada: ukupno 69 od 204 teritorije. Za ostala mesta koristi se jasno označeno geografsko pitanje. Zastava okružnog sedišta nije predstavljena kao zastava okruga.
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
