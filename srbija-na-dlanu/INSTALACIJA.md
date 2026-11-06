# Instalacija i ažuriranje na 2.0.0

1. Preuzmi instalacioni `srbija-na-dlanu.zip` iz direktorijuma `dist` repozitorijuma.
2. WordPress → **Dodaci → Dodaj novi → Otpremi dodatak**. Izaberi ZIP, instaliraj; pri ažuriranju potvrdi **Zameni postojeći dodatak**. Aktiviraj ako je potrebno.
3. Otvori administraciju. Automatska migracija priprema početnu `/srbija-na-dlanu/` i podstranice `okruzi/`, `opstine/`, `baza-znanja/`, `prijava/`, `nalog/`.
4. Očisti Autoptimize i keš stranica/CDN-a. Proveri sve linkove menija, obe igre, prijavu i e-poštu.

Aplikacija koristi sopstveni šablon sa kompasom i menijem. Na ovim stranicama nema zaglavlja/podnožja glavnog sajta ni administratorske trake. Ostale WP stranice zadržavaju temu. Postojeća početna stranica se prepoznaje po putanji; njen sadržaj i Elementor metapodaci se ne brišu. Dodatak ne menja globalnu WP početnu stranicu. Koristi standardne WP permalinke; sa lepim permalinkovima putanje su kao gore.

Ako migracija posle zamene nije pokrenuta, otvori WP administraciju kao administrator. Za administrativnu integraciju postoji autentifikovana POST ruta `/wp-json/srbija-na-dlanu/v1/admin/setup`, dostupna samo nalogu sa `manage_options`; ponovljeno pozivanje ne pravi duple stranice.

## Nalozi

Postojeći WP korisnik prijavljuje se istim korisničkim imenom/e-poštom i običnom lozinkom. WP aplikaciona lozinka služi API-ju i ne koristi se u formi prijave.

Registracija pravi Pretplatnika i šalje WP poruku sa linkom za postavljanje lozinke u istom stilu. Isključi registraciju u meniju **Srbija na dlanu** ako želiš samo postojeće naloge. Proveri da hosting stvarno dostavlja WP e-poštu; dodatak koristi postojeći `wp_mail` i eventualni SMTP dodatak.

Rezultati i aktivne partije prijavljenih korisnika čuvaju se na nalogu. Drugi uređaj nastavlja istu partiju; svaka igra ima svoj dnevni izazov i vežbanje. Gostujuća trenutna partija vezuje se za nalog samo kada nema ranije aktivne partije tog tipa. Lokalna istorija prethodnih gostujućih rezultata ne uvozi se kao serverski verifikovan rezultat.

## Uređivanje sadržaja

Meni **Srbija na dlanu** sadrži sve teritorije. Možeš menjati naziv, druge nazive, sedište, stanovništvo/godinu/izvor, fotografiju i zanimljivost. Za zastavu unesi HTTPS adresu slike iz WP biblioteke, autora, licencu i stranicu izvora. Zastava sedišta okruga i odgovarajućeg grada deli isti zapis. Moguće je vratiti ugrađenu zastavu.

Paket ima 52 zastave: 17 sedišta okruga i 52 opštine/grada (69 teritorija). Za ostala mesta postoji jasno označeno zamensko pitanje. Detalji i izvori su u FLAGS.md. Bez fotografije poslednja runda postaje prostorni izazov. Današnji pripremljeni izazov ne menja se usred dana.

JSON izvoz sadrži geometriju, podatke i trenutnu bazu zastava. Pre uvoza uzmi rezervnu kopiju. WP korisnički podaci, opcije i uploads prenose se standardnom rezervnom kopijom WordPress-a; JSON baze nije rezervna kopija korisničkih naloga ili njihovih rezultata.

## Keš i problemi

- Izuzmi `prijava/`, `nalog/`, REST rute `srbija-na-dlanu/v1/*` i AJAX `snd_session` iz keširanja. Ne keširaj HTML prijavljenih korisnika. API vraća `no-store`.
- Dozvoli JSON POST zahteve i zaglavlje `X-WP-Nonce`. Ako igra ne može da proveri prijavu, proveri da hosting ne blokira `admin-ajax.php?action=snd_session`.
- Stari izgled: očisti Autoptimize, keš stranice/CDN-a i osveži pregledač. Resursi imaju verziju 2.0.0.
- Greška 404 na novim stranicama: **Podešavanja → Permalinks → Sačuvaj izmene**, pa proveri pravila servera.
- Link za lozinku nije stigao: proveri WP dostavu pošte/spam. Korisnik može ponovo zatražiti lozinku preko aplikacije.
- Brisanje podataka pregledača briše gostujuću statistiku. Statistika prijavljenog naloga ostaje u WP bazi.

Za ugradnju igre na neku drugu stranicu i dalje radi `[srbija_na_dlanu]`, odnosno `[srbija_na_dlanu kind="district"]` i `kind="municipality"`. Ta druga stranica koristi sopstveni šablon teme; uklanjanje zaglavlja/podnožja odnosi se na šest stranica aplikacije.

Zahtevi: WordPress 6.2+, PHP 7.4+, JavaScript, WP REST API. Poseban backend, Node.js i API ključevi nisu potrebni. Izvori i licence: SOURCES.md, DATA-LICENSE.md i FLAGS.md. Deaktivacija ne briše podatke.
