# Srbija na dlanu 2.0

Zasebna geografska aplikacija unutar WordPress-a: početna, igre za okruge i opštine, baza znanja i nalozi igrača. Sveden prikaz: slika gore, pitanje i odgovori ispod, sa većim slovima.

**[Preuzmi instalacioni ZIP](https://github.com/cvjetan23/Srbija-na-dlanu/raw/refs/heads/main/dist/srbija-na-dlanu.zip)** · [Instalacija](srbija-na-dlanu/INSTALACIJA.md) · [Provere](docs/PROVERA.md) · [Zastave i licence](srbija-na-dlanu/FLAGS.md)

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

## Sadržaj i igra

- **30 teritorija:** 29 upravnih okruga i Grad Beograd kao posebna celina.
- **174 opštine i grada**, sa Kosovom i Metohijom, bez zasebnih gradskih opština.
- Stvarne siluete, šest pokušaja, udaljenost i strelica od odgovora ka traženoj teritoriji.
- Posle otkrivanja slede **četiri dodatna pitanja**: stanovništvo/položaj, administrativna geografija, zastava i znamenitost/prostorni izazov.
- **52 lokalne zastave** pokrivaju 17 okružnih sedišta i 52 opštine/grada: ukupno 69 od 204 teritorije. Za ostala mesta koristi se jasno označeno geografsko pitanje. Zastava okružnog sedišta nije predstavljena kao zastava okruga.
- **30 lokalnih fotografija znamenitosti**, sa autorima, licencama i izvorima.
- Isti dnevni zadatak za sve, promena u ponoć po vremenu u Srbiji; nasumično vežbanje, nastavak i deljenje rezultata bez rešenja.
- Pretraga prihvata latinicu, ćirilicu i unos bez kvačica.
- Baza znanja: svih 30 teritorija, pretraga po nazivu i sedištu, detalji i samo obris izabranog okruga.

Kosovo i Metohija su uključeni sa **pet okruga i 29 opština** prema podeli korišćenoj u projektu. Popis RZS 2022. tamo nije sproveden; broj stanovnika se ne izmišlja, a pitanje se zamenjuje geografskim. Na ostaloj teritoriji broj stanovnika odnosi se na cele opštine/gradove; zbir je 6.647.003.

## Nalozi i progres

Prijava, odjava i promena lozinke koriste standardne WordPress funkcije i postojeće WP korisnike. Registracija igre pravi samo **Pretplatnika**, bez obzira na globalnu podrazumevanu ulogu; može se isključiti u meniju dodatka. Lozinka se postavlja preko linka u WP e-pošti.

Prijavljenom igraču server čuva aktivnu partiju, upoznate teritorije, dnevne rezultate, vežbanja, kviz i niz dana u WP korisničkim podacima. Drugi uređaj sa istim nalogom nastavlja istu partiju. Dnevna partija se ne resetuje ponovnim otvaranjem. Rezultat se računa na serveru i upisuje jednom. Token privatne partije nije dovoljan drugom korisniku da je otvori.

Gost koristi lokalnu statistiku pregledača i serversku partiju do tri dana. Posle prijave trenutna gostujuća partija može se vezati za nalog, osim kada nalog već ima aktivnu partiju tog tipa. Stara lokalna statistika se ne uvozi kao verifikovani rezultat. Aktivne partije naloga ističu posle 30 dana; ukupni rezultati ostaju na nalogu. Keš može ranije ukloniti WP transient; sačuvani rezultati time se ne brišu.

## Administracija i tehnika

WP meni **Srbija na dlanu** uređuje teritorije, fotografije i potvrđene zastave sa izvorima/licencama; podržan je JSON uvoz/izvoz. Današnji zadatak ostaje nepromenjen posle izmene sadržaja. Deaktivacija ne briše podatke.

WordPress **6.2+**, PHP **7.4+**, nativni JavaScript/CSS. Koriste se postojeći WP options, transients, usermeta, REST API, korisnici, nonces i Media Library. Nema novog servera, posebne baze, npm instalacije ni API ključeva.

Izuzmi iz keširanja prijavu, nalog, `/wp-json/srbija-na-dlanu/v1/*` i `admin-ajax.php?action=snd_session`. Dozvoli JSON POST i `X-WP-Nonce`. Dodatak šalje `no-store` za privatne podatke; `snd_session` proverava stvarni WP kolačić i daje svež REST nonce.

## Izrada i provere

```sh
python3 tools/build-package.py
node tests/frontend.js
WP_ROOT=/putanja/do/izolovanog/wp php tests/game-flow.php
WP_ROOT=/putanja/do/izolovanog/wp php tests/account-progress.php
WP_ROOT=/putanja/do/izolovanog/wp php tests/asset-loading.php
```

PHP testove pokreći samo na namenskoj lokalnoj/razvojnoj WP instalaciji sa aktivnim dodatkom. Testovi kreiraju podatke; nisu namenjeni produkciji. [Izveštaj](docs/PROVERA.md) razlikuje HTTP/HTML provere od vizuelnog testa u pregledaču. Verzija 2.0.0 je postavljena na cvjetan.dev ručnim otpremanjem ZIP-a. Naknadno su prošle 134 produkcijske HTTP provere stranica, obe igre, zastava, nativne prijave/odjave, čuvanja rezultata i nastavka na drugom uređaju. Vizuelna provera u pravom pregledaču i dostava WP e-pošte nisu potvrđene.

Kod je GPL-2.0-or-later. Geometrija, fotografije i zastave imaju zasebne uslove: [SOURCES.md](srbija-na-dlanu/SOURCES.md), [DATA-LICENSE.md](srbija-na-dlanu/DATA-LICENSE.md), [FLAGS.md](srbija-na-dlanu/FLAGS.md). Za dopunske granice Kosova i Metohije javni servis nije naveo posebnu licencu za redistribuciju. Karte služe igri i nisu katastarske karte.
