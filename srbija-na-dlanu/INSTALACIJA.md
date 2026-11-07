# Instalacija i ažuriranje na 3.4.0

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

Od 3.3.0 uz znamenitost postoje polja **Naziv u ponuđenim odgovorima — bez imena grada** i odgovarajuća polja za engleski, nemački i ruski. Ona menjaju tekst opcija u kvizu. Puni naziv znamenitosti i zanimljivost ostaju zasebni, za bazu i objašnjenje posle odgovora. Ugrađena baza koristi 30 neutralnih naziva u svakom jeziku; postojeći identifikatori odgovora se ne menjaju. Broj stalno prikazanih polja prati **Podešavanja → Broj pokušaja**.

Meni **Srbija na dlanu** sadrži sve teritorije. Možeš menjati naziv, druge nazive, sedište, stanovništvo/godinu/izvor, fotografiju i zanimljivost. Za zastavu unesi HTTPS adresu slike iz WP biblioteke, autora, licencu i stranicu izvora. Zastava sedišta okruga i odgovarajućeg grada deli isti zapis. Moguće je vratiti ugrađenu zastavu.

Paket ima 52 zastave: 17 sedišta okruga i 51 opštinu/grad (68 aktivnih teritorija). Za ostala mesta postoji jasno označeno zamensko pitanje. Detalji i izvori su u FLAGS.md. Bez fotografije poslednja runda postaje prostorni izazov. Današnji pripremljeni izazov ne menja se usred dana.

JSON izvoz sadrži geometriju, podatke i trenutnu bazu zastava. Pre uvoza uzmi rezervnu kopiju. WP korisnički podaci, opcije i uploads prenose se standardnom rezervnom kopijom WordPress-a; JSON baze nije rezervna kopija korisničkih naloga ili njihovih rezultata.

## Keš i problemi

- Izuzmi svih šest stranica aplikacije, REST rute `srbija-na-dlanu/v1/*` i AJAX `snd_session` iz keširanja. Ne keširaj HTML prijavljenih korisnika. API vraća `no-store`.
- Dozvoli JSON POST zahteve i zaglavlje `X-WP-Nonce`. Ako igra ne može da proveri prijavu, proveri da hosting ne blokira `admin-ajax.php?action=snd_session`.
- Stari izgled: očisti Autoptimize, keš stranice/CDN-a i osveži pregledač. Resursi imaju verziju 3.2.0.
- Greška 404 na novim stranicama: **Podešavanja → Permalinks → Sačuvaj izmene**, pa proveri pravila servera.
- Link za lozinku nije stigao: proveri WP dostavu pošte/spam. Korisnik može ponovo zatražiti lozinku preko aplikacije.
- Brisanje podataka pregledača briše gostujuću statistiku. Statistika prijavljenog naloga ostaje u WP bazi.

Za ugradnju igre na neku drugu stranicu i dalje radi `[srbija_na_dlanu]`, odnosno `[srbija_na_dlanu kind="district"]` i `kind="municipality"`. Ta druga stranica koristi sopstveni šablon teme; uklanjanje zaglavlja/podnožja odnosi se na šest stranica aplikacije.

Zahtevi: WordPress 6.2+, PHP 7.4+, JavaScript, WP REST API. Poseban backend, Node.js i API ključevi nisu potrebni. Izvori i licence: SOURCES.md, DATA-LICENSE.md i FLAGS.md. Deaktivacija ne briše podatke.


## Ispravke 2.1.0

Posle zamene dodatka otvori administraciju i očisti Autoptimize i keš stranica/CDN-a. Sadržaj naloga i dosadašnji rezultati se čuvaju. Započete partije zadržavaju pokušaje i odgovore; neodgovoreno staro pitanje o regionu zamenjuje se geografskim pitanjem, a već odgovoreno ostaje u pregledu sa svojim rezultatom.

Skripte i mali stilovi aplikacije označeni su noptimize blokovima, jer je minifikacija na produkciji menjala razmake u tekstu. Optimizacija ostatka sajta ostaje uključena. Resursi teme i Elementor prikaza uklanjaju se samo na stranicama aplikacije, uključujući kasno prijavljene resurse i Google fontove teme.

Deljeni link sadrži režim i datum. Datum ne otvara arhivu: stari link prikazuje današnji izazov uz obaveštenje. Igra sada posebno prikazuje ishod teritorije, upotrebu pomoći i niz igranja.

Ako slika ne može da se učita, igrač bira ponovno učitavanje ili zamensko geografsko pitanje. Pregled odgovora sadrži pitanje, svoj izbor, tačan odgovor i sliku tačne zastave. Puni izvor fotografije pre odgovora otvara se kao označena pomoć; autor i licenca su stalno prikazani.

U Bazi znanja je dostupna stranica „Šire celine u igri” (?podela=regioni). To je urednička podela za orijentaciju, ne zvanična regionalizacija i ne bodovano zamensko pitanje. Geometrijske runde navode odgovarajuće izvore geoBoundaries/MRE.

Nov funkcionalni kolačić snd_client (30 dana, HTTP-only, SameSite=Lax) razdvaja ograničenja anonimnih igrača koji dele IP adresu. Ne sadrži rezultate, ime, e-poštu ili lozinku. Nalozi imaju svoja ograničenja, uz viši zajednički prag protiv zloupotrebe.


## Administracija u 3.0.0

U WP meniju **Srbija na dlanu** otvori **Podešavanja** za jezike, temu, boje, veličinu teksta, broj pokušaja, pomoć, dodatne runde, registraciju i beleženje analitike. Promena pravila važi za nove partije vežbanja i buduće dnevne izazove; započete partije ostaju iste.

**Prevodi** uređuju tekst interfejsa. Oznake poput `{count}` sačuvaj tačno; prevodi se čuvaju po jeziku i mogu se vratiti na ugrađene vrednosti. Nazive i zanimljivosti znamenitosti na engleskom, nemačkom i ruskom uređuješ u detalju teritorije. Ako promeniš izvorni opis, dopuni njegove prevode.

**Analitika**, **Igrači** i **Partije** prikazuju aktivnosti, zbirni progres i stvarne pokušaje/odgovore. Dostupni su datum, igra, režim, faza, jezik i ID igrača kao filteri, kao i CSV/JSON izvoz. Detaljna istorija beleži se od instalacije 3.0.0; stariji dnevni rezultati se uvoze u manjim WP-Cron paketima kao zbirni zapisi. Stari zbir vežbanja ostaje u progresu, bez izmišljenih pojedinačnih partija.

Kreiraju se dve tabele u postojećoj WP bazi. Podrazumevani rok za analitiku je 365 dana, promenljiv u Podešavanjima; 0 znači neograničeno. Dnevni WP-Cron čisti isteklu analitiku, a progres ostaje na nalogu. Bez WP-Cron saobraćaja zakazani uvoz/čišćenje čekaju sledeće pokretanje. Rezervna kopija WordPress-a treba da obuhvati i nove tabele.

Standardni WP alati **Izvoz ličnih podataka** i **Brisanje ličnih podataka** obuhvataju progres i analitiku igre. Izvoz administracije sadrži lične podatke igrača i dostupan je samo administratorima. Deaktivacija ne briše podatke.


## Mape i izbor prikaza u 3.1.0

Baza znanja prvo prikazuje mapu Srbije. Klik na okrug otvara njegovu mapu sa opštinama; klik na opštinu otvara obris, podatke i položaj u okrugu. Ispod mapa ostaju spiskovi sa istim linkovima. Poseban tretman Beograda i obuhvat Kosova i Metohije ostaju isti.

Izbor jezika u zaglavlju prikazuje pun naziv aktivnog jezika; otvaranjem se vide svi omogućeni jezici. Pored njega su dugmad sa suncem, mesecom i automatskim izborom teme. Podrazumevani jezik, omogućeni jezici, tema i pravo korisnika da je menja i dalje se podešavaju kroz Podešavanja dodatka. Menjanje jezika čuva trenutno otvorenu opštinu/okrug i režim igre.

Nisu potrebne nove WP stranice ili dodatni API ključevi. Detalji koriste postojeću Bazu znanja sa parametrima `okrug` i `opstina`. Nakon zamene dodatka otvori administraciju i očisti keš.


## Tabele, animacije i veza u 3.2.0

Spisak opština sada je tabela sa nazivom, brojem stanovnika i godinom popisa. Podaci ostaju oni iz urednika teritorija. Za Kosovo i Metohiju, gde podaci Popisa RZS 2022. ne postoje, prikazuje se jasna oznaka umesto izmišljenog broja.

Učitavanje prikazuje animirani kompas. Promena jezika, pitanja i teme ima blag prelaz; smanjeno kretanje u podešavanjima uređaja isključuje animacije. Bez JavaScript-a rade izvorni linkovi i obrazac teme.

Ako se REST veza prekine (npr. prikazani HTTP/2 problem), klijent pokušava isti zahtev kroz `admin-ajax.php` sa akcijom `snd_game`. Vlasništvo, REST nonce prijavljenog korisnika, ograničenje zahteva i zaključavanje poteza ostaju isti. Jedna revizija prihvata samo jedan potez, a početak koristi request_id za povrat izgubljenog odgovora. Potrebno je dozvoliti JSON i form POST zahteve prema standardnom WP AJAX endpointu.


## Beogradske opštine od 3.4.0

Dodaje se svih 17 gradskih opština, uključujući Vračar, Zvezdaru, Novi Beograd, Palilulu, Surčin i ostale. Granice su iz sloja MRE, a stanovništvo iz beogradskog bloka tabele RZS 2022 (Palilula u Nišu je drugi red). Zbir 17 opština iznosi 1.681.405. U igri opština ima 190 aktivnih teritorija; cela teritorija Beograda više se ne dodaje njihovom zbiru. Grad Beograd ostaje u igri okruga.

Dodatak dopunjuje i ranije sačuvane/uvežene baze bez prepisivanja postojećih lokalnih izmena. Svaka nova opština može se zasebno isključiti u uređivaču teritorija. Ako se isključe svih 17, vraća se raniji obuhvat sa celim Beogradom. JSON izvoz uključuje nove opštine, njihove izvore i matičnu celinu. Zapis `m-beograd` čuva istoriju i prethodne partije i u administraciji je označen kao istorijski.

Već započeta i pripremljena dnevna partija ne menja geometriju, pitanja ili identifikatore. Beogradske opštine dostupne su u novim partijama vežbanja i novim dnevnim izazovima. Starija započeta partija nudi jasno dugme za novo vežbanje sa dodatim opštinama; bez tog izbora se ne resetuje. Staro dostignuće za ceo Beograd ostaje u istoriji, ali se ne računa kao rešenih 17 opština.
