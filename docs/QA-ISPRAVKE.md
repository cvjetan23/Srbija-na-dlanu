# Ispravke dva QA izveštaja — 2.1.0

Pripremljeno 6. novembra 2026. Izveštaji sa 14 i 17 nalaza imaju više istih problema; ispod su objedinjene ispravke konkretnih grešaka i poboljšanja jasnoće, pitanja i efikasnosti.

| Nalaz | Ispravka |
|---|---|
| Minifikacija spaja tekst, uključujući deljenje | Resursi aplikacije zaštićeni su izvornim Autoptimize noptimize blokovima; osetljivi dinamički tekstovi sastavljaju se eksplicitnim razmacima i novim redovima. Stalno ostaje uključena optimizacija ostatka sajta. |
| Deljeni dnevni link otvara vežbanje | URL sadrži mode=daily/practice i datum dnevne partije; režim iz URL-a ima prednost nad podešavanjem. Stari datum otvara današnji izazov uz jasnu poruku, bez obećanja arhive. |
| Prvi ArrowUp označava pretposlednji predlog | Početno stanje se obrađuje posebno: ArrowUp bira poslednji, ArrowDown prvi predlog. |
| Dodir za skrolovanje odmah bira opštinu | Biranje je prebačeno na potvrđen click; pointermove/pointercancel i prag pomeranja razlikuju prevlačenje od kratkog dodira. Dodir ne sprečava vertikalno skrolovanje. |
| Ponovljen odgovor izgleda kao nepostojeći | Pretraga daje posebnu poruku kada su odgovarajuće teritorije već pokušane. |
| Lokalni JSON pogrešnog oblika prekida učitavanje | Proveravaju se oblik podešavanja i ugnježdenih nizova statistike; null, brojevi, stringovi i neodgovarajući nizovi vraćaju bezbedne podrazumevane vrednosti. |
| Izvor fotografije otkriva znamenitost pre odgovora | Autor/licenca ostaju vidljivi. Puni izvor se pre odgovora prikazuje tek posle označene akcije pomoći, zabeležene na serveru i u pregledu; izvor nije poslat u neodgovorenom pitanju. Posle odgovora je dostupan. |
| × stoji uz tačan odgovor, nema pitanja ni izbora igrača | Pregled prikazuje pitanje, status Tačno/Netačno/Preskočeno, tvoj izbor, zasebno tačan odgovor, objašnjenje i izvore. Pogrešan marker nije zakačen za tačan odgovor. |
| U pregledu i posle osvežavanja nema tačne zastave | Server šalje tačnu sliku i jasan naziv, frontend ih čuva u prikazu završene partije i obnovljenom pregledu. |
| Kvar fotografije/zastave nema jasno rešenje | Ponuđeni su ponovno učitavanje i serversko zamensko geografsko pitanje; odgovor se ne bira dok slike ne rade, pokušaji se ne troše, a tehnička zamena je zabeležena u pregledu. |
| Pomoć otkriva odgovor na kasnije regionalno pitanje | Zamena za zastavu sada pita približno rastojanje centara, sa izvorima. Neodgovoreno staro regionalno pitanje migrira bez resetovanja pokušaja; već ocenjeni odgovor i rezultat ostaju sačuvani. |
| Ishod je crtica, a Bez pomoći zvuči kao uspeh | Posebno su prikazani Pogođeno, Odgovor otkriven i Nije pogođeno za šest pokušaja. Pomoć se prikazuje odvojeno kao korišćena/nije korišćena. |
| Niz nije jasno definisan, „1 dana” | Naziv je Niz igranja; pravilo broji završene dnevne partije, uključujući otkrivanje/preskakanje. Igra i nalog pravilno prikazuju 1 dan, 11 dana i 21 dan. |
| Runda 1/6 pa 2/5 zbunjuje | Prva runda ima odvojene oznake Runda 1/5 i Pokušaj 1/6; kviz nastavlja numeraciju rundi. |
| Uputstvo obećava vizuelni sadržaj koji nije svuda dostupan | Početna i pomoć preciziraju prepoznavanje i četiri opciona pitanja. Obe vrste geografskih zamena su dosledno označene; fotografije/zastave se ne izmišljaju. |
| Pogrešne populacije/teritorije su previše očigledne | Populacione alternative su približno istog reda veličine; geografski izbori uzimaju bliže, uverljivije teritorije. Jedan odgovor ostaje tačan. Izvorne popisne vrednosti nisu promenjene. |
| Regionalna podela nije objašnjena; pogrešan/odsutan izvor | Baza znanja dobija tabelu Šire celine u igri, jasno označenu kao uredničku podelu za pomoć. Geometrijska pitanja navode geoBoundaries, MRE ili oba izvora prema korišćenim teritorijama. |
| Isteklo vežbanje nudi današnji izazov; istek prijave isto dugme | Oporavak razlikuje novu partiju vežbanja, današnji izazov i obnovu prijave. Obnova prijave čuva ključ aktivne partije. |
| Novi dnevni token može ostati pod jučerašnjim datumom | Granica dana osvežava bootstrap prema vremenu servera; čuvanje tokena koristi datum prihvaćenog stanja partije. |
| Loš kontrast/sitan tekst; bela zastava se stapa sa podlogom | Pomoćni tekst, izvori i placeholder su potamnjeni i uvećani. Zastava dobija blag okvir/podlogu; postojeći veći fontovi ostaju. |
| Čitač ekrana duplira naziv zastave | Slika unutar dugmeta je dekorativna (prazan alt), vidljiv naziv daje jedno pristupačno ime. Element za najavu stanja sada se popunjava. |
| Velika ugrađena tema/builder CSS količina | Resursi teme, Elementor stilovi iz uploads, fontovi teme i neupotrebljeni core blok-stilovi uklanjaju se samo u aplikaciji, i u kasnim fazama štampanja. Sopstveni stilovi idu odvojeno; nevezane sigurnosne integracije se zadržavaju. |
| Cela mapa se ponavlja tokom kviza | Kviz više ne šalje kompletnu mapu ni katalog za pretragu; mapa je prisutna samo na otkrivanju i rezultatu, kada se prikazuje. |
| Škola/kancelarija deli prenizak IP limit | Normalne kvote odvojene su po nalogu/gostujućem klijentu, uz viši zajednički IP prag. Registracija više nema zajednički prag od četiri osobe; postoje i individualni pragovi protiv zloupotrebe. |
| Naslovi, opis, canonical i jezik schema podataka nisu usklađeni | Naslovi koriste Srbija na dlanu; detalj okruga ima svoj naslov, opis i canonical sa identifikatorom. Usklađeni su Open Graph i sr-Latn strukturirani podaci, uz podršku za prisutni Slim SEO i bez menjanja ostatka sajta. |

## Izvršena verifikacija

GitHub Actions izolovani WordPress 7.1.4 i PHP 8.3.6, uz uključene Autoptimize i Slim SEO dodatke: **5.415 automatskih provera**.

| Skup | Broj |
|---|---:|
| Postojeći backend/podaci, svih 204 teritorije | 2.837 |
| Nalozi, zastave i zaključavanje | 148 |
| Učitavanje resursa i Elementor regresije | 10 |
| Postojeći frontend tok | 24 |
| Novi događaji/stanja frontenda i kontrast | 64 |
| Nove WordPress QA regresije | 2.332 |

Provere obuhvataju sintaksu, oba skupa generisanih pitanja, veličinski uverljive alternative, izvore geometrije, izostanak odgovora pre vremena, pomoć za izvor, zamenu neispravnih medija, pregled zastave posle nastavka, stare sesije, odvajanje grupnih limita, metadata, stvarni Autoptimize hide/restore i regionalnu tabelu. Testovi ne iscrpljuju limite na produkciji.

Svih 30 okružnih celina, 174 opštine/grada i Kosovo i Metohija ostaju u bazi. Popisni podaci nisu menjani. Banka sadržaja ostaje na 30 fotografija i 52 zastave; dalje prikupljanje fotografija i potvrđenih zastava predstavlja dopunu sadržaja, a ne rešavanje ovih konkretnih bagova.

## Granice i postavljanje

Nova verzija nije vizuelno pregledana u pravom pregledaču ili na fizičkom telefonu, niti je testirana uz NVDA/VoiceOver. Dodirni događaji, tastatura i prelazak ponoći provereni su kontrolisanim regresionim testovima. Produkcijska dostava registracionih/reset poruka nije testirana i nisu slati stvarni mejlovi.

Verzija 2.1.0 isporučuje se kroz repozitorijum i instalacioni ZIP. Na cvjetan.dev je prethodno potvrđena 2.0.0; novu verziju treba postaviti zamenom dodatka i čišćenjem keša. Nedostupno radno okruženje sprečilo je direktno produkcijsko otpremanje u ovom koraku; razvoj, testovi i pakovanje završeni su preko GitHub-a.
