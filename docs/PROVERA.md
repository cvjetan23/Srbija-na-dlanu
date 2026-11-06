# Provera paketa — Srbija na dlanu 1.1.0

- WordPress 7.1.4, PHP 8.4.27, SQLite kroz standardni wpdb interfejs.
- Dodatak učitan i aktiviran iz standardnog wp-content/plugins/srbija-na-dlanu direktorijuma.
- Prošlo 2.222 provere: sadržaj svih 204 teritorije, četiri jedinstvena izbora u svakom pitanju, popisni zbir i pripadnost okruzima, API tok partije, nevažeći i ponovljeni potezi, stale revisions, pomoć, završetak, nastavak, dnevna nepromenljivost i validacija uvoza.
- Popisni zbir svih 145 opština i gradova sa podacima jednak je zvaničnom zbiru Popisa 2022: 6.647.003.
- Kosovo i Metohija: pet okruga i 29 opština, bez izmišljenih podataka Popisa 2022.
- PHP i JavaScript sintaksa proverene; pretraga proverena za oba pisma i unos bez kvačica.
- Ispravka 1.0.1 prošla je dodatnih 10 provera učitavanja CSS-a: Elementor metapodaci, odbačen prethodni render, footer fallback, bez duplih stilova i bez učitavanja na drugim stranicama. Isti test je pre ispravke otkrio nedostajuće stilove.
- U verziji 1.1.0 provereno je osam stanja novog prikaza: početak, promašaj, otključana pomoć, otkrivanje teritorije, kviz, odgovor u kvizu, fotografija i rezultat. Provereni su redosled slika–pitanje, izbor oba režima, uslovna pomoć, četiri ponuđena odgovora i zaključavanje već odgovorenog pitanja. Ovo je provera generisanog HTML-a, ne test u pregledaču.
- Na verziji 1.1.0 ponovo prolazi svih 10 postojećih regresionih provera učitavanja resursa u WordPress-u. Backend i baza nisu menjani u ovoj verziji.
- Svih 30 fotografija pregledano; primerci koji nisu pokazivali traženi objekat zamenjeni su. Fotografije se isporučuju lokalno, sa izvorima i licencama.
- ZIP je proveren: jedan glavni direktorijum dodatka, 30 fotografija, bez razvojnih zavisnosti i testne WordPress instalacije.

Nije rađena vizuelna provera u pravom pregledaču niti provera novog prikaza u tvojoj temi. Testna SQLite integracija nije deo paketa i nije potrebna na tvom WordPress-u; dodatak koristi uobičajene WP funkcije i tvoju postojeću bazu. O uslovima geografskih izvora pročitaj DATA-LICENSE.md u dodatku.

Naknadna HTTP provera objavljene verzije 1.0.0 na cvjetan.dev i ispravka 1.0.1 opisane su u LIVE-CHECK.md. Novi prikaz 1.1.0 pripremljen je lokalno i isporučen kroz repo i ZIP. Za proveru novog prikaza na produkciji potrebno je da korisnik ažurira WordPress dodatak. Browser alat nije bio dostupan u razvojnom okruženju, pa vizuelna provera u pravom pregledaču nije izvršena.
