# Provera objavljene igre i ispravka 1.0.1

Proverena javna stranica: https://cvjetan.dev/srbija-na-dlanu/ — 6. novembar 2026.

## Nalaz na verziji 1.0.0

- Stranica vraća HTTP 200 i sadrži shortcode kontejner igre.
- Dodatak je ugrađen u Elementor tekstualni widget, uz Astra temu i Autoptimize.
- Originalni i Autoptimize JavaScript vraćaju HTTP 200 i JavaScript tip sadržaja. Optimizovani JavaScript prolazi proveru sintakse.
- Originalni CSS fajl postoji i vraća HTTP 200, ali ga nema među stilovima same stranice. HTML ne sadrži ni `snd-game-css` link ni pravila `.snd-game`.
- CSS izostaje i kada se stranica zatraži sa podržanim Autoptimize parametrom `ao_noptimize=1`; problem zato nije samo u CSS agregaciji.
- `bootstrap` endpoint vraća HTTP 200, JSON i `no-store` zaglavlje, sa 30 celina za okruge i 174 opštine i grada, uključujući Kosovo i Metohiju.
- Kroz javni API proverene su dve anonimne partije vežbanja, po jedna u svakoj igri: početak, pokušaj, otkrivanje teritorije, tri dodatna pitanja i završetak. Obe su prošle.

Ove provere koriste javni HTML, resurse i API. Nije korišćen pravi pregledač niti su ocenjeni raspored, boje ili ponašanje dodira.

## Ispravka

Verzija 1.0.1 prepoznaje shortcode i u Elementor `_elementor_data` metapodacima, pa se stilovi prijavljuju na vreme za zaglavlje.

Shortcode više ne štampa CSS link u sopstveni izlaz. Prethodno renderovanje koje builder odbaci zato više ne može označiti CSS kao već odštampan. Za sadržaj dodat kasnije postoji štampanje CSS-a u stvarnom `wp_footer` hook-u, pre footer skripti, uz proveru protiv dupliranja.

Verzija resursa promenjena je na 1.0.1 radi osvežavanja keša. Sadržaj igre, baze i sačuvane dnevne partije nisu menjani.

## Provera ispravke

Regresioni test je pre izmene pao na Elementor stranici čiji `post_content` nema shortcode, a metapodaci ga sadrže. Posle izmene prolazi svih 10 provera učitavanja resursa. Prošlo je i svih 2.222 postojećih provera na čistom WordPress-u 7.1.4 / PHP-u 8.4.27.

## Postavljanje na sajt

1. Preuzmi novi `dist/srbija-na-dlanu.zip` iz repozitorijuma.
2. U WordPress-u: **Dodaci → Dodaj novi → Otpremi dodatak**, pa izaberi **Zameni postojeći dodatak**.
3. Očisti Autoptimize i eventualni keš stranice/CDN-a, pa ponovo učitaj stranicu.

U trenutku ove provere nije postojala autentifikovana veza sa WordPress administracijom. Ispravka se isporučuje kroz repozitorijum i ZIP; GitHub commit sam po sebi ne ažurira instalirani dodatak na cvjetan.dev.

## Priprema 2.0.0 — 6. novembar 2026.

Nove stranice, WP nalozi/progres, baza znanja i runda zastava pripremljeni su u repozitorijumu i instalacionom ZIP-u. Provere su opisane u PROVERA.md. Običnu administratorsku prijavu trenutno zaustavlja DreamWeb anti-bot provera, pa nova verzija nije postavljena. WP aplikacioni API pristup radi, ali core endpoint za dodatke ne prima proizvoljan instalacioni ZIP. Produkcijski tok nove verzije i dostava WP poruka nisu potvrđeni.

## Potvrđena produkcija 2.0.0 — 6. novembar 2026.

Posle vlasnikovog ručnog otpremanja ZIP-a javni API potvrđuje verziju 2.0.0 i 30/174 teritorija. Kreirane su i objavljene stranice: početna (1529), Okruzi (1541), Opštine (1542), Baza znanja (1543), Prijava (1544), Moj nalog (1545). Podstranice su ispod `/srbija-na-dlanu/`. Nije bilo potrebe za dodatnim pozivanjem administratorske setup rute.

Prošle su 134 produkcijske HTTP provere. Provereni su svi detalji okruga, oba toka igre do rezultata, četiri bonus runde, stvarne lokalne zastave, ćirilična pretraga, isporuka stilova i skripti kroz Autoptimize, odvojeno zaglavlje/podnožje aplikacije i očuvana Astra tema na glavnoj stranici.

Prijava na novoj stranici aplikacije radi sa datim privremenim WP nalogom. Provereni su vezivanje gostujuće partije za nalog, upis rezultata jednom, pregled progresa, nastavak iste partije u drugoj nezavisnoj WP sesiji, zaštita privatnog tokena od gosta i odjava. Dodato je jedno završeno vežbanje privremenom nalogu. Lozinke, kolačići, REST nonce i tokeni nisu sačuvani u repozitorijumu ili izveštaju.

Ovo su HTTP i HTML provere; browser alat nije dostupan. Vizuelna provera u pravom pregledaču i dostava WP poruka nisu potvrđene.
