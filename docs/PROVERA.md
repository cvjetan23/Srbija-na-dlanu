# Provera verzije 2.1.0

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
