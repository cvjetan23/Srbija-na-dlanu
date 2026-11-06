# Provera paketa — Srbija na dlanu 1.0.1

- WordPress 7.1.4, PHP 8.4.27, SQLite kroz standardni wpdb interfejs.
- Dodatak učitan i aktiviran iz standardnog wp-content/plugins/srbija-na-dlanu direktorijuma.
- Prošlo 2.222 provere: sadržaj svih 204 teritorije, četiri jedinstvena izbora u svakom pitanju, popisni zbir i pripadnost okruzima, API tok partije, nevažeći i ponovljeni potezi, stale revisions, pomoć, završetak, nastavak, dnevna nepromenljivost i validacija uvoza.
- Popisni zbir svih 145 opština i gradova sa podacima jednak je zvaničnom zbiru Popisa 2022: 6.647.003.
- Kosovo i Metohija: pet okruga i 29 opština, bez izmišljenih podataka Popisa 2022.
- PHP i JavaScript sintaksa proverene; pretraga proverena za oba pisma i unos bez kvačica.
- Ispravka 1.0.1 prošla je dodatnih 10 provera učitavanja CSS-a: Elementor metapodaci, odbačen prethodni render, footer fallback, bez duplih stilova i bez učitavanja na drugim stranicama. Isti test je pre ispravke otkrio nedostajuće stilove.
- Svih 30 fotografija pregledano; primerci koji nisu pokazivali traženi objekat zamenjeni su. Fotografije se isporučuju lokalno, sa izvorima i licencama.
- ZIP je proveren: jedan glavni direktorijum dodatka, 30 fotografija, bez razvojnih zavisnosti i testne WordPress instalacije.

Nije rađena vizuelna provera u pravom pregledaču niti provera novog prikaza u tvojoj temi. Testna SQLite integracija nije deo paketa i nije potrebna na tvom WordPress-u; dodatak koristi uobičajene WP funkcije i tvoju postojeću bazu. O uslovima geografskih izvora pročitaj DATA-LICENSE.md u dodatku.

Naknadna HTTP provera objavljene verzije 1.0.0 na cvjetan.dev opisana je u LIVE-CHECK.md. Verzija 1.0.1 je pripremljena i proverena lokalno; za proveru novog prikaza na produkciji potrebno je da korisnik ažurira WordPress dodatak.
