# Produkcija — verzija 4.1.2

Verzija **4.1.2 je postavljena i aktivna** na https://cvjetan.dev/srbija-na-dlanu/ — 10. novembar 2026. Nativni WP installer je potvrdio uspešnu zamenu; WP API potvrđuje aktivan dodatak 4.1.2.

- Konačni izvor `860360490069139b008640aaa31184c896fb96f7`, paket `d39ed1019d0ae2ede7c283c80c87715e7b102f15`; svih **199 datoteka** odgovara lokalnom manifestu. [CI izvora i paketa](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42819371420) prolazi 58.192 WP, 141 JS i 754 Chromium tvrdnji. Dokumentacioni završetak ne menja dodatak ili ZIP.
- ZIP SHA256 `3dbb0caaa144c416b55045ee1d504465534aac61d194f639505cf818f9fc960f`; **30.749.111 bajtova, 475 datoteka dodatka**. Instaliran je isti provereni ZIP sa grane main.
- **374 javnih HTTP tvrdnji**: svi glavni pogledi na četiri jezika, padajući meni sa pet igara, brojači uklonjeni, opis ispod igara, dashboard, no-store, deset tačnih CSS/JS resursa i odgovarajući javni katalozi. API: 30 okružnih celina, **197 opštinskih jedinica**, 255 naselja, **63 potvrđene reke**, 188 planina. Potpun dnevni fond novih igara ostaje 226/61/52. Svih 28 gradskih opština i KiM su uključeni.
- **203 produkcijskih Chromium tvrdnji**: pet kompletnih gostujućih dnevnih partija, km/smer, prikaz promašenog toka/pina, četiri dodatna pitanja, zastave, fotografije, uvećanje, boje odgovora, rezultat/nastavak, baze sa nezavisnim izvorima, nova Medijana, filteri levo/rezultati desno i mobilni prikazi 320 px u četiri jezika i obe teme. Nema JS izuzetaka. Posebno je potvrđeno da Autoptimize čuva redosled novih stilova, da su mobilne igre u jednoj koloni i da su brojevi pravila na ilustracijama. Nema upisa rezultata na naloge/rang listu; provera koristi goste. Fizički telefon nije korišćen.
- Svi WP pogledi analytics/players/games/settings/translations/ranking/explore vraćaju 200 bez PHP grešaka. Paleta, Sunrise, 6 pokušaja, težine 60/6/10 i runde population/administration/flag/landmark su očuvani.
- Privatni nacrt „Agla” dostupan je uredniku, a odsutan iz javne baze i ponuđenih odgovora, uključujući zamrznuti dnevni katalog. Današnji zadatak/progres nisu resetovani; dopunjeni fond učestvuje u narednim dnevnim izborima. [Provera izvora](KATALOG-4.1.md).
- Administratorska sesija je nativno odjavljena i memorijski proces zatvoren nakon provere.

---

# Produkcija — verzija 4.0.0

Verzija **4.0.0 je postavljena i aktivna** na https://cvjetan.dev/srbija-na-dlanu/ — 8. novembar 2026. Nativni WordPress installer je vratio 200, očekivani SHA256 i „Plugin updated successfully“. WP API potvrđuje aktivan dodatak 4.0.0.

- Konačni izvor `f89faea6b8ef7d20d8e339350cc7d3faafaa3b51`, stablo `cd9039de68f5380f456332ad562942e71252c3e7`. Paket `48fc7e74ee5012213a7386cc6a7c044937e2e14d`, stablo `ff631b6526edba6823988cdae5d97f89fb30a742`. Svih **190 datoteka** poređeno je sa lokalnim manifestom po Git blob SHA i veličini. Naknadni izveštaj menja samo dokumentaciju.
- ZIP: **30.678.303 bajta, 471 datoteka**, SHA256 `e9cdfd33b500b51e976b4b1aa5dde7ee8d70fe9301256b5a8e6fa6777838641e`. Isti hash potvrđuju lokalna izgradnja, GitHub CI, repozitorijum i nativna WP zamena. Fotografije su lokalne: 337 proverenih kopija, sa autorima, licencama i izvorima. Izvorni ZIP paketi sprečavaju razlike pri kasnijem generisanju Wikimedia sličica.
- [Izvorni CI i izgradnja](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42525092843) su prošli: **56.094 WordPress, 141 JavaScript, 620 Chromium tvrdnji**. [Main CI](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42525706631) radi nad istim izvorom i paketom.
- **341 javna HTTP tvrdnja**: 40 pogleda u četiri jezika, četiri glavna linka menija, kompaktne jezičke/tematske kontrole, no-store, devet identičnih CSS/JS resursa i svih pet aktivnih kataloga. API: 30 okružnih celina, 190 opština, 255 naselja, 475 reka, 188 planina; spreman dnevni fond 226/61/52 za nove igre. KiM ostaje uključen.
- **171 produkcijska Chromium tvrdnja**: svih pet dnevnih igara od početka do rezultata kao gost; šest numerisanih pokušaja, boje i smer, stvarni pinovi/tokovi promašaja, map-zum, fotografije i zastave sa lupicom bez slanja odgovora, povratak fokusa, bodovi i četiri pregledana odgovora, nastavak po osvežavanju i odsustvo ponovnog igranja. Potvrđen je vidljiv PSS obuhvat pitanja o planini. Baza znanja otvara katalog i detalj svakog novog tipa; mobilni meni i rang lista su provereni u četiri jezika i obe teme. Snimci grada/reke/mobilnog prikaza su pregledani. Nema JS izuzetaka; TLS provera je uključena.
- Svih **sedam administrativnih prikaza** je zdravo: analitika, igrači, partije, podešavanja, prevodi, rang lista i geografski katalozi. Paleta, runde sa zastavama, šest pokušaja i težine 60/6/10 su očuvani. Tokom produkcijske provere završeno je pet gostujućih partija, uz nekoliko parcijalnih proba proverača. Progres i javni poeni naloga nisu menjani. Sesija za održavanje je odjavljena i zatvorena.

[Raspored igara i rangiranja](NOVE-IGRE.md) · [Izvori i granice kataloga](../srbija-na-dlanu/EXPLORE-SOURCES.md). Mobilni prikaz je emuliran; fizički telefon i isporuka e-pošte nisu deo provere.

# Prethodna produkcija — verzija 3.8.1

Verzija **3.8.1 je postavljena i aktivna** na https://cvjetan.dev/srbija-na-dlanu/ — 8. novembar 2026. Nativni WordPress obrazac vratio je 200, očekivani hash i „Plugin updated successfully“.

- Izvor `ae89514897be1c90fd4f7457aeecd7344d781563`, stablo `3ed3a9189742860ed2ea5f2579b86c7d806984c0`. Paket `3fcf69bd82b877459b181b45c4b711986468739d`, stablo `1706e58b3d94dba61beb01bf288d406b4aa9502d`. Svih **169 datoteka** paketa odgovara lokalnom manifestu po Git blob SHA i veličini.
- ZIP SHA256: `b3baf7fdc6331bf748e22485bfdd809f48c0ac6ba2095e729d7e719c5426ffad`; **7.953.565 bajtova, 127 datoteka dodatka**. Završni dokumentacioni commit ne menja dodatak ni ZIP.
- Prošle su [provere izvora](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42512723173) i [main](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42513104102): **33.197 WordPress, 141 JavaScript i 483 Chromium tvrdnje**. Nova serija ima 151 tvrdnju za kompaktno zaglavlje u četiri jezika i obe teme, na 320/640/1024/1440 px.
- **202 javne produkcijske HTTP tvrdnje** proveravaju šest pogleda na četiri jezika, jezičke i tematske kontrole, pet stavki menija i nalog, no-store i osam identičnih CSS/JS resursa. API vraća 3.8.1, 30 okružnih celina i 190 opština/gradova/gradskih opština, uz očuvan KiM.
- **152 produkcijske Chromium tvrdnje**: jezik prati levu ivicu, tema desnu, navigacija počinje levo, nalog ostaje desno u prvom redu; kontrole su kompaktnije i ne preklapaju se. Proverena je širina 640 px sa korisnikove slike i 320 px, oba desktop rasporeda, četiri jezika i obe teme, tastatura, otvaranje jezičkog menija i njegova širina, očuvanje okruga pri promeni jezika i native promena jezika/teme bez JS. Pregledan je produkcijski snimak zaglavlja. Nema JS izuzetaka; TLS provera je uključena.
- Svih **šest administratorskih prikaza** vraća 200 bez PHP grešaka. Potvrđene su iste boje, runde, pokušaji i težine bodovanja kao pre ažuriranja. **Nijedna partija nije kreirana**, progres i poeni naloga nisu menjani. Administratorska sesija je odjavljena i zatvorena.

[Gradovi/naselja, reke, planine i zajednička dnevna rang lista](NOVE-IGRE.md) ostaju razrađen koncept za pregled; nisu predstavljeni kao dostupne funkcije ovog izdanja. Fizički telefon i produkcijska isporuka e-pošte nisu deo provere. Broj tvrdnji nije broj nezavisnih scenarija.

# Prethodna produkcija — verzija 3.8.0

Verzija **3.8.0 je postavljena i aktivna** na https://cvjetan.dev/srbija-na-dlanu/ — 7. novembar 2026. Nativni WordPress obrazac potvrdio je uspešno ažuriranje i očekivani hash ZIP-a.

- Proveren izvor: `0536df56e720ad990790ce3cb98fb719683c0037`. Proveren paket: `47bb9543de182b96142056d26579c3d9ead07652`, stablo `89897a8be94ed60e8b6a39832160fffd7829c03b`. Svih **167 datoteka** paketa poređeno je sa lokalnim izvorom po Git blob SHA i veličini. Završni dokumentacioni commit ne menja dodatak ili ZIP.
- ZIP SHA256: `13484da40fdc0595471812984cb193b900b943fc3ac855e959f3af154664b6d5`; **7.953.782 bajta i 127 datoteka dodatka**. WordPress upload vratio je isti hash i „Plugin updated successfully“.
- Prošle su [provere konačnog izvora](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42400382241) i [main](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42400877778): **33.197 WordPress, 141 JavaScript i 332 Chromium tvrdnje**, uključujući 56 provera zajedničkih kontrola, boja odgovora i lupice.
- **1.824 javne HTTP tvrdnje** pokrivaju šest pogleda na četiri jezika, 72 kombinacije rang liste, baze znanja i tabele, svih 17 beogradskih opština, KiM, metapodatke, no-store i identične postavljene CSS/JS/SVG resurse. API potvrđuje 3.8.0, 30 okružnih celina i 190 opština/gradova/gradskih opština.
- **77 produkcijskih Chromium tvrdnji**: zajednički oblik i boja glavnih dugmadi, filteri i formulari, obe teme, četiri jezika na 320 px, reke preko ekrana, kružni loader, mali brojevi i zvezdica, Beograd/Vračar, potezi bez posvetljivanja, sve dodatne runde i bodovi. U tekstualnom pitanju i zastavama potvrđene su zelena popuna tačnog i Sunrise popuna izabranog netačnog odgovora.
- Svaka lupica je ikonica preko slike, bez vidljivog teksta. Njen okvir ostaje unutar slike na velikoj i mobilnoj širini. Klik pre i posle odgovora otvara dijalog bez slanja odgovora; Escape i zatvaranje vraćaju fokus. Slike ostaju neobojene i čitljive. Pregledani su produkcijski snimci zastava i rang liste. Nema JavaScript izuzetaka, TLS provera je uključena.
- Svih **šest administratorskih prikaza** vraća 200 bez PHP grešaka. Potvrđene su postojeće boje, četiri uključene runde, šest pokušaja i težine 60/6/10. Promenjeni su samo opisi uloga boja u podešavanjima; vrednosti nisu resetovane.
- Provera je napravila **dve gostujuće partije vežbanja**, bez novih rezultata ili promena progresa naloga za održavanje. Administratorska sesija je odjavljena i zatvorena.

Fizički telefon i produkcijska dostava e-pošte nisu deo ove provere. Broj tvrdnji nije broj nezavisnih scenarija ili uređaja.

# Prethodna produkcija — verzija 3.7.0

Verzija **3.7.0 je postavljena i aktivna** na https://cvjetan.dev/srbija-na-dlanu/ — 7. novembar 2026. Nativni WordPress upload potvrdio je uspešno ažuriranje i očekivani hash paketa. Nova paleta „Šuma i sunce“ primenjena je na obe teme.

- Proveren izvor: `8680a780b974181b0d84a4bb521c4d8d82d745db`. Proveren paket: `a3d7614b17ecf91936fd59a5f5b1bc0269df06f3`, stablo `69b98b695434004f8d3fa130d84fefe7e12d2a87`. Svih **167 datoteka** proverene grane i ZIP-a odgovara lokalnom izvoru po Git blob SHA i veličini.
- ZIP SHA256: `a4fec079d3d6104594e6c13448f03946686602f231b2fd411f893574704b2a14`; **7.953.090 bajtova i 127 datoteka dodatka**. Završni dokumentacioni commit ne menja dodatak ili ZIP.
- Prošle su [GitHub Actions provere konačne grane](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42391198247) i [main](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42391515313): WordPress 7.1.4, PHP 8.3.6, MySQL 8.0.46, Autoptimize, Slim SEO; 33.197 WordPress, 141 JavaScript i 314 Chromium tvrdnji. Raniji kandidat takođe je prošao CI, a konačni dodatno ispravlja duge prevode dugmadi na mobilnoj početnoj.
- **1.824 javne HTTP tvrdnje** pokrivaju šest pogleda na četiri jezika, oba tipa rang liste sa svim periodima/igrama, baze znanja i tabele, beogradske opštine i KiM, metapodatke, no-store i tačne postavljene CSS/JS/SVG bajtove. API vraća verziju 3.7.0, 30 okružnih celina i 190 opština/gradova/gradskih opština.
- **66 produkcijskih Chromium tvrdnji**: borova i topla svetla pozadina, zeleni CTA sa belim natpisom i malim Sunrise detaljem, ceo ekran reka na 2560 px i pri skrolovanju, pravila, četiri mobilna prevoda početne, Beograd/Vračar, potezi bez posvetljivanja table, dodatne runde sa zastavama, uvećanje bez odgovora, Escape/fokus i zatvaranje na telefonu, ispravan odgovor sa istom površinom, bodovi, obe rang liste i četiri jezika. Opštine imaju mali broj/zvezdicu pri promeni širine, uključuju svih 17 beogradskih opština. Nema JavaScript izuzetaka; TLS provera uključena.
- Svih **šest administratorskih prikaza** vraća 200 bez PHP grešaka. Potvrđene su sve nove boje, četiri uključene runde, šest pokušaja i težine 60/6/10. Nova kontrola vraća samo predložene boje; njeno stvarno čuvanje i očuvanje ostalih podešavanja provereni su na izdvojenom WP-u.
- Posle postavljanja pregledani su produkcijski snimci obe teme i toka igre. Kreirane su **dve gostujuće partije vežbanja**, bez novih rezultata ili promena progresa drugih naloga. Administratorska sesija za održavanje odjavljena je i zatvorena.

Istraživanje izbora boja i njihove uloge: [PALETA-3.7.md](PALETA-3.7.md). Fizički telefon i produkcijska isporuka e-pošte nisu deo ove provere. Broj tvrdnji nije broj nezavisnih scenarija.

# Prethodna produkcija — verzija 3.6.0

Verzija **3.6.0 je postavljena i aktivna** na https://cvjetan.dev/srbija-na-dlanu/ — 7. novembar 2026. Nativni WP obrazac potvrdio je uspešno ažuriranje dodatka. Zadržana je paleta glavnog sajta uz Sunrise.

- Proveren izvor: `9e72ed3e9acf1142109b69094cdfac00e830fab9`. Proveren paket: `0ae02b9a2234d01b81ec9f071af81f409f455e66`, stablo `eab9f1a7546cdbddc7891af358e5219a0701137c`. Svih **165 datoteka** proverene grane i paketa poklapa se sa lokalnim izvorom po Git blob SHA i veličini.
- ZIP SHA256: `39241faa25e5458204573ee0d3eaa04bedc175fbbcb5c0fc0fc853c940e4285f`; **7.952.324 bajta, 127 datoteka dodatka**. WordPress upload je vratio isti hash i „Plugin updated successfully“.
- Prošle su [GitHub Actions provere grane](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42388064667) i [main](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42388489304): WordPress 7.1.4, PHP 8.3.6, MySQL 8.0.46, Autoptimize, Slim SEO, 33.194 WordPress, 141 JavaScript i 307 Chromium tvrdnji.
- **1.824 produkcijske HTTP tvrdnje** proverile su šest javnih pogleda na četiri jezika, oba tipa rang liste sa svim periodima/igrama, mape i tabele, beogradske opštine, KiM, metapodatke, no-store i identične postavljene CSS/JS/SVG resurse. API vraća 3.6.0, 30 okružnih celina i 190 opština/gradova/gradskih opština.
- **61 tvrdnja u stvarnom produkcijskom Chromium-u**: paleta glavnog sajta, ravna dugmad i beli natpisi, Sunrise strelice, cela pozadina na 2560 px pri skrolovanju, početna i pravila na 320 px, kružni loader, Beograd/Vračar, pokušaji bez gubitka kontrasta, sve dodatne runde, zastave i uvećanje bez slanja odgovora, Escape/fokus, Sunrise okvir uz jednaku pozadinu, rezultat i bodovi, opštine sa svih 17 beogradskih, male numerisane oznake pri promeni širine i obe rang liste na četiri jezika. Nema JavaScript izuzetaka. TLS provera je uključena.
- Svih **šest administratorskih prikaza** vraća 200 bez PHP grešaka. Podešavanja prikazuju sedam osnovnih boja i dva dodatna polja tamne teme. Potvrđene su četiri uključene runde, šest pokušaja i težine 60/6/10. Stare podrazumevane boje osvežene su kroz migraciju; namerno prilagođene vrednosti se čuvaju.
- Produkcijski snimci početne, rezultata opština i zastava pregledani su posle postavljanja. Provera je napravila **dve gostujuće partije vežbanja**, bez novog rezultata administratorskog/tehničkog naloga. Drugi nalozi, progres i bodovi nisu menjani. Administratorski nalog za održavanje odjavljen je i sesija zatvorena.

Fizički telefon i produkcijska isporuka e-pošte nisu deo ove provere. Broj tvrdnji nije broj nezavisnih scenarija. Završni dokumentacioni commit ne menja proverene datoteke dodatka i ZIP.

[Pregled svakog elementa](PREGLED-3.6.md).

# Prethodna produkcija — verzija 3.5.1

Verzija **3.5.1 je postavljena i aktivna** na https://cvjetan.dev/srbija-na-dlanu/ — 7. novembar 2026. Nativni WordPress obrazac potvrdio je uspešno ažuriranje dodatka; nova stranica je https://cvjetan.dev/srbija-na-dlanu/rang-lista/.

- Proveren izvor: `bc8772a3a307729c368e7fcf9b2654d216424d8f`. Proveren izvor i instalacioni paket: `363e0218030a2cfb766e226d70aea2b113c0f1f3`. Svih **163 datoteke** proverene grane, uključujući ZIP, poređene su sa lokalnim izvorom po Git blob SHA i veličini.
- ZIP SHA256: `a86eb5e01a977188b0dbb9371ead976f23972ae5b8bca019b7d8a2f396e9bb65`; **7.951.759 bajtova i 127 datoteka dodatka**.
- Prošle su [provere izdvojene grane](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42379900773) i [main](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42380534293), sa WordPress 7.1.4, PHP 8.3.6, MySQL 8.0.46, Autoptimize i Slim SEO. Kompletne PHP/JS provere, novih 1.278 tvrdnji bodovanja/rangiranja i svih **276 Chromium tvrdnji** su prošli.
- Nakon konačne zamene prošle su **1.824 javne HTTP tvrdnje**. Obuhvaćeni su šest javnih pogleda na četiri jezika, samostalno zaglavlje/podnožje, meni sa rang listom i ikonicom naloga desno, pravila i njihovi SVG prikazi, tačne mape/tabele i svih 17 beogradskih opština. Ostaje 30 okružnih celina, 190 aktivnih opština/gradova, pet okruga i 29 opština KiM, bez izmišljenog popisa.
- Oba tipa rang liste proverena su za sva tri perioda i sve tri igre/obuhvata, na sva četiri jezika: **72 kombinacije**. Provereni su aktivni izbor, tabela, ispravan prikaz i no-store. Aktuelni CSS/JS, `ranking.css` i `rivers.svg` poklapaju se po bajtovima sa proverenim paketom.
- Još **21 tvrdnja u stvarnom produkcijskom Chromium-u** proverila je konačni cache-busting 3.5.1, beli tekst dugmadi, pravila i reke, pet stavki menija, sva četiri jezika i mesečnu listu na 320 px. Septembar i godina staju uz ikonicu kalendara. Tehnički rezultat ostaje skriven sa javne liste; nema JavaScript grešaka. TLS provera je bila uključena.
- U punoj funkcionalnoj proveri neposredno pre sitne ispravke polja meseca prošlo je **67 HTTP tvrdnji**, sa dve gostujuće partije. Provereni su tri promašaja i tačan četvrti pokušaj, geometrija svih pokušaja, četiri dodatne runde uključujući stvarnu zastavu, neutralni nazivi znamenitosti, tačno serversko bodovanje, 190 opština i prihvatanje Vračara, završetak/nastavak i oba transporta bez dupliranja.
- Prošlo je i **46 produkcijskih Chromium tvrdnji**, sa jednom gostujućom partijom, na širinama 1440 i 320 px: početna, pravila, mapa/tabela Beograda i Vračar, pravilni krug i overlay, pokušaj bez posvetljivanja table, sve četiri dodatne runde, uvećanje zastave pre/posle odgovora, fokus/Escape, SVG kvačica, bodovi i čitljivi brojevi stare kartice rezultata, oba tipa liste/sva tri perioda i četiri jezika. Ispravka 3.5.1 nije menjala ovaj backend ili tok igre.
- Zasebno je izvršena **jedna bodovana partija tehničkog naloga za održavanje** kroz standardnu WP prijavu i native obrazac nadimka/privatnosti. Nalogu je sakriven javni plasman pre početka. Prvi pogodak i jedan tačan dodatni odgovor dali su **70/100**; server je ignorisao klijentski poslate poene. Progres se uvećao jednom, dva nastavka nisu duplirala upis, rezultat postoji u administratorskoj listi i nije vidljiv na javnoj. Progres drugih naloga nije menjan.
- Svih **šest administratorskih prikaza** (Analitika, Igrači, Partije, Podešavanja, Prevodi, Rang lista) vraća 200 bez PHP grešaka. Potvrđene su četiri uključene runde, šest pokušaja i podešavanja 60/6/10, #FFD447 i tamna pozadina dugmadi.

Produkcijska provera ukupno je napravila **tri gostujuća vežbanja i jedno privatno vežbanje tehničkog naloga**. Konačna provera 3.5.1 nije dodavala nove partije. Obe sesije održavanja odjavljene su i zatvorene. Započete partije, ranija istorija i dnevna pravila ostaju sačuvani; obaveštenje nudi novo vežbanje sa bodovanjem i obnovljenom rundom zastava.

U odvojenom pregledu ispravljeni su dodatno kontrast starih brojčanih rezultata, odsečene kolone na telefonu, početni loader bez JavaScript-a i zavisnost stilova igre u odbačenom builder renderu. Automatizovano kliktanje SVG mape sada čeka da link prima klik i da je DOM odredišta spreman. [Pregled svakog elementa](PREGLED-3.5.md).

Fizički telefon i produkcijska isporuka e-pošte nisu proveravani. Broj tvrdnji nije broj nezavisnih uređaja ili scenarija. Produkcijski pregledi koriste normalnu proxy konfiguraciju i uključenu TLS proveru.

# Prethodna produkcijska provera — verzija 3.4.0

Verzija **3.4.0 je postavljena i aktivna** na https://cvjetan.dev/srbija-na-dlanu/ — 7. novembar 2026. WordPress je potvrdio uspešno ažuriranje kroz standardni obrazac za zamenu dodatka.

- Proveren izvor: `08fa7f34f402e665352464c1511553964dc4877a`. Proveren izvor i paket: `8e67603db9230db23a22f7594eeb13431448136d`. Svih 155 datoteka proverene grane poređeno je sa lokalnim izvorom po Git blob SHA i veličini.
- ZIP SHA256: `25978f4344c266d7f0991635ba1b57405a7928a3b2c0915429ba49613ab700d6`; 7.928.931 bajt i 123 datoteke dodatka.
- Prošle su [GitHub Actions provere izdvojene grane](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42365028170) i [main](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42365500983), sa WordPress 7.1.4, PHP 8.3.6, MySQL 8, Autoptimize i Slim SEO. Lokalno i u CI-ju prošlo je 226 tvrdnji u stvarnom Chromium-u.
- Prošlo je **1.270 javnih HTTP tvrdnji**: pet pogleda na četiri jezika, skraćen uvod, četiri ilustrovana pravila pre baze znanja, istaknute akcije Igraj, metapodaci i privatni keš, tačne tabele i mape Beograda, Šumadijskog i svih pet okruga KiM, kao i svih 17 novih beogradskih opština sa stanovništvom i izvorima. Stari link `?opstina=m-beograd` vraća 301 ka detalju Beograda sa 17 opština.
- Produkcijski resursi poklapaju se po bajtovima sa proverenim paketom. API vraća 3.4.0, 30 okružnih celina i **190 aktivnih opština/gradova/gradskih opština**, bez dvostrukog prikaza celog Beograda. Kosovo i Metohija ostaju obuhvaćeni; nije dodat izmišljeni Popis RZS 2022.
- Još **64 HTTP tvrdnje** proverile su dva gostujuća vežbanja. Okrug: tri promašaja i tačan četvrti odgovor, sve četiri dodatne runde (uključujući zastavu i znamenitost), pregled i nastavak. Opštine: katalog sa svih 17 novih identifikatora, pokušaj Vračar, završna mapa sa 190 teritorija, završetak i nastavak. Provereni su REST i WP AJAX, idempotentan start, odbijanje ponovljenog poteza i oporavak stanja.
- Prošlo je **25 tvrdnji u stvarnom produkcijskom Chromium-u**, sa uključenom TLS proverom i normalnom proxy konfiguracijom. Na širinama 1440 i 320 px provereni su početna i pravila, meni i nalog, mapa/tabela Beograda, klik na Vračar i njegovih 55.406 stanovnika, unos iznad pokušaja, stvarni potezi, otkrivanje i sve četiri dodatne runde. Uvećanje zastave radi pre i posle odgovora, bez slanja poteza; Escape vraća fokus. Kružna kvačica je SVG uz naziv. Nije bilo grešaka aplikacije u ovom toku.
- Pregledani su produkcijski snimci početne u tamnoj temi, pitanja sa zastavama i oznakama, uvećane zastave na mobilnoj širini, detalja Vračara i rezultata. Dodatnih 57 lokalnih/CI Chromium tvrdnji pokriva jače isticanje zadatog okruga i njegove posebne oznake uz svih šest promašaja, četiri jezika, kontrast, stare partije i dobrovoljni prelazak na novo vežbanje.
- Svih pet administratorskih prikaza (Analitika, Igrači, Partije, Podešavanja, Prevodi) vraća 200 bez PHP grešaka. Administratorska sesija je odjavljena nakon provera.

Produkcijske provere napravile su **tri gostujuća vežbanja**: dva HTTP klijentom i jedno kroz Chromium. Progres postojećih naloga i podešavanja nisu menjani testovima. Započete i pripremljene dnevne partije čuvaju raniji katalog; nove opštine dostupne su u novim partijama. Ovo je provereno i na izdvojenoj instalaciji sa 354 backend tvrdnje za beogradske opštine, istoriju, prilagođene podatke i stare dnevne partije.

Fizički telefon i produkcijska isporuka e-pošte nisu proveravani. Broj tvrdnji nije broj nezavisnih scenarija ili uređaja. Ranija ograničenja produkcijskog Chromium-a, navedena ispod za prethodne verzije, ne odnose se na ovu proveru 3.4.0.

# Prethodna produkcijska provera — verzija 3.3.0

Verzija **3.3.0 je postavljena i aktivna** na https://cvjetan.dev/srbija-na-dlanu/ — 7. novembar 2026. WordPress je potvrdio uspešno ažuriranje kroz standardni obrazac za zamenu dodatka.

- Proveren izvor: `809ebc5fb7e1c3b4e6b91c64c34164c089fcd2e3`. Proveren izvor i paket: `0b7b5e6a0c9a6a0993c2f1f791d80faec6c25fd2`. Svih 151 datoteka proverene grane poređeno je sa lokalnim izvorom po Git blob SHA i veličini.
- ZIP SHA256: `51d2f3aeee5cb4796d1a2450fa7ceb91cd6245a50693ac00668bf0e2ab71f328`; 7.894.697 bajtova i 122 datoteke dodatka.
- [GitHub Actions za izdanje](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42360742134) prošao je sa MySQL 8, WordPress 7.1.4, PHP 8.3.6, Autoptimize i Slim SEO. Prošla je i [provera na main](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42360995658).
- Prošle su **1.023 javne HTTP tvrdnje**: svi pogledi u četiri jezika, odvojena ikonica naloga posle menija i izbora prikaza, tabele/stanovništvo/godine, 30 okružnih celina, 174 opštine/grada sa KiM, oblici i linkovi, metapodaci i privatni keš.
- Produkcijski `app.css`, `game.css`, `game.js`, `ui.js`, `library.js`, `themes.css`, `atlas.css` i `motion.css` poklapaju se po bajtovima sa proverenim paketom. API vraća 3.3.0.
- Još **51 HTTP tvrdnja** proverila je jednu novu gostujuću partiju: tri promašaja, tačan četvrti odgovor, geometriju svakog pokušaja na završnoj mapi, populaciju, sedište, zastavu i fotografiju znamenitosti. Ponuđeni nazivi znamenitosti jesu neutralni; podatak o tačnom odgovoru sa zvaničnim nazivom dostupan je posle odgovora. Završen kviz ima sva četiri pregleda odgovora.
- Ista partija koristila je oba transporta. Ponovljeni start vratio je isti token, ponovljeni potez staru reviziju/409, a nastavak prihvaćeni rezultat bez ponavljanja. Napravljeno je jedno gostujuće vežbanje; produkcijski progres naloga i pravila igre nisu menjani testom.
- Svih pet administratorskih prikaza dostupno je sa statusom 200, bez PHP grešaka. Administratorska sesija je odjavljena nakon provera.

U lokalnom i MySQL CI Chromium-u prošlo je **169 tvrdnji**, uključujući novu seriju od 56 provera u svetloj/tamnoj temi i na mobilnoj širini 320 px. Proverene su boje pre/posle tri promašaja, oslobađanje busy stanja i nakon odbijenog poteza, jednake visine unosa i dugmeta, redovi 1–6, SVG strelice i ikonice, sloj padajuće liste, nalog na desnoj ivici, progres uz izvore i brojevi svih pokušaja na mapi i rezultatu. Sve četiri dodatne runde proverene su bez zaostalog posvetljivanja. Pregledani su stvarni snimci svetle/tamne teme i mobilnog ruskog prikaza.

Backend je dodatno proverio neutralne nazive u sva četiri jezika, jedinstvene ponuđene odgovore, skrivanje rešenja pre odgovora, očuvane identifikatore starih pitanja, zvanične nazive u pregledu i sanitizaciju novih administratorskih polja: 2.530 tvrdnji. Lokalni HTTP tok prijave, izvoza, podešavanja i privatnosti prošao je sa 301 tvrdnjom.

Fizički telefon i produkcijska isporuka e-pošte nisu proveravani. Produkcijski vizuelni pregled 3.3.0 nije izvršen u Chromium-u: ranija provera u istom Cloud okruženju pokazala je `ERR_CERT_AUTHORITY_INVALID` kroz proxy, a TLS provera nije isključivana. Produkcija je proverena HTTP klijentom sa uključenom TLS proverom, uz identične resurse potvrđene u lokalnom i CI Chromium-u. Broj tvrdnji nije broj nezavisnih scenarija ili uređaja.

# Prethodna produkcijska provera — verzija 3.2.0

Verzija **3.2.0 je postavljena i aktivna** na https://cvjetan.dev/srbija-na-dlanu/ — 7. novembar 2026. ZIP je zamenjen standardnim WP obrascem sa stvarnim nonce-om; WordPress je potvrdio uspešno ažuriranje.

- Proveren izvor: `da038b59305ac6c83cb055c09c2cdcd3443c86aa`. Proveren izvor i reproducibilni paket: `07e8cb5c871bd6fa114ed571ad33e58fbce25458`. Svih 148 datoteka grane poređeno je sa lokalnim izvorom po Git blob SHA i veličini.
- ZIP SHA256: `6527251d8d266d28438e5e9dd3ca0532f103974d1527bc5fea0adcea16c64a6d`; 7.889.271 bajt i 121 datoteka dodatka.
- [GitHub Actions za izdanje](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42355736395) prošao je sa MySQL 8, WordPress 7.1.4, PHP 8.3.6, Autoptimize i Slim SEO. Prošla je i [provera na main](https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42355974248).
- Prošlo je **1.000 javnih produkcijskih HTTP tvrdnji**: pet pogleda na četiri jezika, metapodaci, no-store, odvojeno zaglavlje/podnožje, meni bez oznaka Jezik/Tema, kompas za učitavanje, prikaz i tačne vrednosti tabela za Beograd, Šumadijski i svih pet okruga KiM, kao i oblici i linkovi opština. Broj tvrdnji nije broj nezavisnih scenarija ili uređaja.
- `game.js`, `ui.js`, `library.js`, `themes.css`, `atlas.css` i `motion.css` na produkciji poklapaju se po bajtovima sa proverenim paketom. API vraća 3.2.0, 30 okružnih celina i 174 opštine/grada. Nema izmišljenog popisa za KiM.
- Još **devet produkcijskih HTTP tvrdnji** proverilo je WP AJAX bootstrap/start/action/resume, no-store, idempotentan start, odbijanje ponovljenog poteza i završetak. Napravljeno je jedno gostujuće vežbanje; progres postojećih naloga nije menjan tim testom. Raniji dijagnostički HTTP/2 spot-check takođe je napravio gostujuće vežbanje.
- Svih pet WP prikaza dodatka (Analitika, Igrači, Partije, Podešavanja, Prevodi) vraća 200 i sadržaj bez PHP grešaka. Administratorska sesija odjavljena je nakon provera.

Lokalno i u MySQL CI-ju prošlo je **113 provera u stvarnom Chromium-u**. Obuhvaćeni su klikovi na SVG, četiri jezika, teme, tastatura/dodir, prikazi 320/390/768/1440 px, tabele, reduced motion, stvarna WP prijava i odgovor izgubljen nakon prihvaćenog poteza. Rezervni zahtev odbijen je zbog stare revizije, zatim je nastavak vratio prihvaćen potez bez lažne greške i uz jedan upis rezultata. Snimci tabela i mobilnog prikaza pregledani su. Fizički telefon i produkcijska isporuka e-pošte nisu proveravani.

Produkcijski Chromium kroz Cloud proxy nije mogao da potvrdi lanac TLS sertifikata (`ERR_CERT_AUTHORITY_INVALID`); TLS provera nije isključivana. Zato su produkcijske provere izvršene HTTP klijentom sa uključenom TLS proverom, uz poređenje stvarnih resursa sa paketom proverenim u lokalnom i CI Chromium-u.

Povremeni `ERR_HTTP2_PROTOCOL_ERROR` sa korisnikovog snimka nije ponovljen u produkcijskom spot-check-u: `giveup` i `finish` vratili su HTTP/2 200. Verzija dodaje bezbedan oporavak kroz nativni WP kanal; ne menja HTTP/2 konfiguraciju hostinga.

# Prethodna produkcijska provera — verzija 3.1.0

Verzija **3.1.0 je postavljena i aktivna** na https://cvjetan.dev/srbija-na-dlanu/ — 6. novembar 2026. Paket je zamenjen kroz standardni WP obrazac i potvrđeno je uspešno ažuriranje. Ostali dodaci i progres nisu menjani.

- Provereni izvor i ZIP su na `main`; izdanje: `cf030ea2aa33803844e8295db54f3c859bb09d7c`.
- SHA256 ZIP-a: `6646237ce526307cb250ed367281a5cb3eb16bfabe044569ba2a9b320eb8c432` (120 datoteka).
- GitHub Actions prošao je sa MySQL-om, Autoptimize i Slim SEO, uključujući stvarni Chromium: https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42300284605 . Provera na `main` takođe je prošla.
- Prošla je **631 produkcijska HTTP tvrdnja**: svih pet javnih pogleda u četiri jezika, puni nazivi jezika, tri dugmeta teme, kompas i sopstveno zaglavlje, canonical/hreflang i privatni keš.
- Mapa Srbije i spisak imaju svih 30 okružnih celina. Mape i spiskovi opština provereni su za Beograd, Šumadijski i svih pet okruga Kosova i Metohije u sva četiri jezika. Klikabilni linkovi otvaraju opštinu, njen obris i istaknut položaj; linkovi za promenu jezika čuvaju odabranu opštinu i okrug.
- Produkcijski `ui.js`, `library.js`, `themes.css` i `atlas.css` poklapaju se po bajtovima sa proverenim paketom. Potvrđeni su izbor tamne teme kroz nativni GET obrazac i ažuriran prikaz bez parametara URL-a.
- Svih pet administratorskih pogleda se otvara uz HTTP 200 bez PHP grešaka. Nisu napravljene nove partije tokom ove produkcijske provere.

Na izolovanom WP-u prošlo je **25.846 tvrdnji**: 25.312 PHP, 135 Node, 98 stvarni Chromium i 301 HTTP. Pregledani su Chromium snimci svetle i tamne teme i mobilnih dimenzija; fizički telefon nije korišćen. Chromium provera produkcije nije izvršena: pri HTTPS pristupu kroz proksi pregledač je vratio ERR_CERT_AUTHORITY_INVALID; TLS provera nije isključivana. Produkcijski HTTP proveren je standardnim HTTPS klijentom sa uključenom proverom sertifikata. Dostava WP e-pošte nije deo ove izmene. Detalji novog toka: [ATLAS-3.1.md](ATLAS-3.1.md).

# Produkcija — verzija 3.0.0

Verzija **3.0.0 je postavljena i aktivna** na https://cvjetan.dev/srbija-na-dlanu/ — 6. novembar 2026. ZIP je otpremljen i postojeći dodatak zamenjen kroz standardni WordPress obrazac, uz prijavu preko forme aplikacije. Ostali dodaci nisu menjani.

- Provereni izvor i instalacioni ZIP objavljeni su na `main`; izdanje: `1bfd38d4dc97614a0ce100b23352e2fc8ea078e0`.
- SHA256 instalacionog ZIP-a: `571bdf621ddf5279331174dfbae2b3bc1436634520e9f32c6fa402bea63c5c44`.
- GitHub Actions prošao je na MySQL-u sa Autoptimize i Slim SEO: https://github.com/cvjetan23/Srbija-na-dlanu/actions/runs/42295142488 . Provera na `main` takođe je prošla.
- Produkcijski WordPress API potvrdio je aktivan dodatak 3.0.0. `bootstrap` potvrđuje svih 30 okružnih celina i 174 opštine/grada, sa Kosovom i Metohijom.
- Prošlo je **317 produkcijskih HTTP tvrdnji**: pet javnih pogleda u sva četiri jezika, kompas i sopstveni meni, odsustvo Astra zaglavlja/podnožja, četiri jezika i tri teme u izboru, jezik HTML-a, jedan naslov, četiri hreflang linka i `no-store` zaglavlja.
- Provereni su prevodi baze sa 30 kartica i detalji Beograda, Šumadijskog i svih pet okruga Kosova i Metohije u sva četiri jezika, sa jednim obrisom i pripadajućim opštinama.
- Inline rečnici jezika pravilno se parsiraju uz aktivan Autoptimize. Produkcijski `i18n.js`, `ui.js` i `game.js` po bajtovima odgovaraju proverenom paketu.
- Odigrane su dve gostujuće partije vežbanja, po jedna za okruge na engleskom i opštine na ruskom: početak, otkrivanje, četiri pitanja, lokalne slike kada su ponuđene, označena pomoć preko izvora fotografije, odgovori/objašnjenja, rezultat i ponovno otvaranje. Neodgovorena pitanja nisu otkrivala rešenje.
- Svih pet novih administratorskih pogleda — Analitika, Igrači, Partije, Podešavanja i Prevodi — vraćaju HTTP 200 i očekivani prikaz bez PHP grešaka.

Funkcionalne produkcijske provere napravile su **dve stvarne gostujuće partije vežbanja**, koje mogu biti vidljive u početnoj analitici. Administratorska podešavanja nisu menjana tokom provere. Nije korišćen pravi pregledač niti fizički telefon; vizuelni prikaz tamne teme i produkcijska isporuka e-pošte nisu nezavisno potvrđeni. Registracija/lozinka, promena teme kroz JS događaje, administratorsko čuvanje i izvoz provereni su na izolovanoj WP instalaciji.

# Prethodna provera objavljene igre i ispravka 1.0.1

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
