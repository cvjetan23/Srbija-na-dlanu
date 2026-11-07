# Završni pregled 3.5.0

Odvojen prolaz posle implementacije, 7. novembra 2026. Predprodukcijska provera koristi izdvojeni WordPress, stvarni Chromium i snimke desktopa i mobilnog prikaza. Produkcijska evidencija se vodi u [LIVE-CHECK.md](LIVE-CHECK.md).

| Element | Provera i ispravka |
|---|---|
| Početna i pravila | Brojevi su pomereni na ilustracije. Kartice prate dužinu teksta, bez praznog dna koje je nastajalo zbog rastezanja cele vrste. |
| Meni i nalog | Rang lista je zaseban link; nalog ostaje ikonica sasvim desno. Pet linkova, kompas, jezici i teme staju na desktop i mobilni prikaz. |
| Tamna tema | Dugmad imaju belu oznaku na tamnozelenoj pozadini. Žuti #FFD447 je na malim akcentima. Dodatni pregled ispravio je slabo vidljive brojeve 1/6 i 4/4 u staroj kartici rezultata. |
| Svetla tema | Zadržani su tamni tekst i čitljivi obrasci; žuta je dekorativni naglasak. |
| Pozadina | Samo pet reka i njihove male oznake, iz stvarnih Natural Earth linija; bez granica ili velikih natpisa. Element ne prima klikove i nije sadržaj čitača ekrana. Može se isključiti u WP-u. |
| Loader | Tačan krug 88×88 px sa sopstvenom pozadinom; sloj pokriva ceo viewport. Početak, potezi, oporavak i navigacija imaju završetak. Navigacija prikazuje sloj odmah, bez odloženog tajmera koji pregledač može zamrznuti pri prelasku. |
| Smanjene animacije | Reduced-motion zaustavlja iglu i prelaze. Bez JavaScript-a početni loader je sakriven i prikazana je poruka; baza i rang lista rade kroz obične linkove/formulare. |
| Obe igre | Unos iznad šest numerisanih pokušaja; dugme i polje su poravnati. Nema zaostalog posvetljivanja table nakon pokušaja ili greške. |
| Mapa rezultata | Svaki pokušaj ima broj; traženi okrug/opština imaju izdvojen naziv i marker. Uklonjen je suvišno ponovljen naziv ispod nove kartice bodova. |
| Beogradske opštine i KiM | 190 aktivnih opština/gradova, 17 beogradskih opština, 30 okružnih celina, svih pet okruga i 29 opština KiM. Nema izmišljenog popisa. |
| Zastave | Nova podrazumevana runda koristi četiri stvarne slike za svaku aktivnu teritoriju. Nedostatak lokalne zastave znači jasno imenovanu zastavu sedišta/drugog mesta, bez pogrešnog pripisivanja. Stari odgovori i njihove oznake ostaju validni. |
| Uvećanje i oznake | Uvećanje radi pre/posle odgovora, bez slanja poteza ili otkrivanja tačnog izbora. Escape vraća fokus. Kvačica, strelice izvora i oznaka unosa koriste SVG umesto zavisnosti od posebnih znakova fonta. |
| Dodatne runde | Stanovništvo, pripadnost, zastava, znamenitost/zamena. Neutralni ponuđeni nazivi znamenitosti ne otkrivaju grad. Izvori i licence ostaju uz odgovor. |
| Bodovi | Server računa 0–100. Prvi pogodak 60, dodatni pokušaj −6, kviz do 40; pomoć −10, pitanje uz pomoć izvora 0. Bez dodatnih rundi teritorija vredi 100. Proverene su nule, maksimum, preskakanje i prilagođena pravila. |
| Autoritet rezultata | Klijentski poeni se ignorišu. Potez sa starom revizijom je odbijen. Rezervacija prvog dnevnog pokušaja i jedinstveni ID partije sprečavaju dupliranje. Nastavak i ponovljeni zahtev ne dodaju bodove. |
| Dve odvojene liste | Dnevni izazovi broje najviše jednu završenu partiju po igri i danu. Opšta lista uključuje i sva vežbanja. Veći broj partija na opštoj listi može doneti više poena, što je napisano uz izbor. |
| Vreme i igre | Dan, mesec i sve vreme; sve igre, okruzi i opštine. Izbor arhivskog dana/meseca, stvarne kalendarske granice i vreme Srbije. Završeno vežbanje pripada danu završetka. |
| Izjednačenja i stranice | Jednaki zbirovi dele mesto i kada se protežu preko granice 25 redova. Sledeći manji zbir dobija odgovarajući takmičarski redni broj. |
| Tabela na telefonu | Dodatni pregled zamenio je odsečene kolone: broj partija i prosek prikazuju se ispod nadimka, a mesto i poeni ostaju u kolonama. Izbor datuma je kompaktan na desktopu. |
| Prijava i progres | Nativni WP nalozi i nastavak sa drugog uređaja. Gost vidi bodove, ali za rangiranje treba da bude prijavljen pre početka. Preuzimanje gostujuće partije čuva progres bez naknadnog rangiranja. |
| Nadimak i privatnost | Podrazumevani pseudonim ne otkriva ime za prijavu ili e-poštu. Sopstveni nadimak se sanitizuje. Stvarni obrazac može sakriti postojeće i buduće rezultate; tuđi nalog nije moguće menjati. |
| Administracija | Pregled i CSV, zbir po igrama/režimima, podešavanje težina, boja, pozadine i zastava. Pretplatnik nema pristup pregledu/izvozu; pogrešan nonce vraća 403. |
| Čuvanje i brisanje | Bodovi ne zavise od uključenosti/roka analitike. Čišćenje uklanja samo stare nezavršene rezervacije; završeni bodovi ostaju. WP privatni izvoz i brisanje uključuju tabelu i profil. |
| Stara istorija | Nema naknadno izmišljenih bodova. Započeta partija i dnevna pravila ostaju zamrznuti; jasno obaveštenje nudi novo vežbanje sa bodovanjem i obnovljenim zastavama, bez prisilnog resetovanja. |
| Resursi teme i buildera | Izdvojena MySQL provera otkrila je povratnu zavisnost novih stilova od igre u prethodno odbačenom renderovanju. Zavisnost je okrenuta: igra zavisi od stilova bodova. Regresija potvrđuje da druge stranice ne dobijaju stil igre. |
| Jezici, izvori i SEO | Nova stranica i bodovanje su prevedeni na sva četiri jezika; izbori čuvaju jezik. Rang HTML nema privatne ID-eve ili adrese. Canonical ostaje osnovna rang stranica, a promenljivi prikaz se ne kešira. |
| Ažuriranje | Paket proverava sadržaj svake datoteke, verziju i SHA256. Produkcija se postavlja tek nakon provera; onda slede novi pregled javnih stranica i administracije, stvarna gostujuća partija i odjava održavanja. |

Brojevi tvrdnji u [PROVERA.md](PROVERA.md) opisuju testove, ne broj nezavisnih uređaja. Mobilni prikaz proverava Chromium; fizički telefon i produkcijska dostava e-pošte nisu deo ovog pregleda.
