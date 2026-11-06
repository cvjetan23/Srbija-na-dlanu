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

Verzija **2.0.0 nije automatski postavljena na cvjetan.dev**. Aplikaciona WP lozinka potvrđuje API pristup, ali dostupni API ne podržava zamenu proizvoljnog ZIP-a dodatka. Pokušaj obične administratorske prijave zaustavlja DreamWeb anti-bot stranica pre WordPress-a. Nije pokušano zaobilaženje te provere. Potrebna je dozvoljena prijava kroz hosting ili ručno otpremanje spremnog ZIP-a. Produkcijski prikaz, prijava i dostava WP pošte moraju se proveriti posle stvarnog postavljanja.

Ponovljive PHP/JS provere su u `tests/`; pokretati ih samo na namenskoj lokalnoj/razvojnoj WP instalaciji. HTTP testni harness je korišćen u izolovanom radnom okruženju. Ograničenja i licence geografskih izvora opisani su u DATA-LICENSE.md.
