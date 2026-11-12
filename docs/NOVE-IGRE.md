# Pet dnevnih igara i vežbanje — 4.2.0

Implementacija dnevnih igara za okruge, opštine, gradove i naselja, reke i planine. Isti zadatak za sve igrače i jezike, promena dana u zoni Europe/Belgrade. Svaka igra ima šest pokušaja, zatim četiri dodatna pitanja. Neuspešno pogađanje ne sprečava nastavak obrazovnih pitanja.

| Igra | Početak i promašaj | Četiri dodatna pitanja |
|---|---|---|
| Okruzi | Obris; udaljenost i smer | Stanovništvo/geografija, pripadnost, zastava sedišta, znamenitost |
| Opštine | Obris; udaljenost i smer | Stanovništvo/geografija, pripadnost, zastava ili proverena zamena, znamenitost |
| Gradovi i naselja | Referentni pin naselja; broj promašaja, km i smer ka cilju | Okrug, stanovništvo naselja ili region, reka ili smer prema drugom mestu, fotografija |
| Reke | Stvarni tok u Srbiji; pogrešna reka se iscrtava i numeriše | Neposredni recipijent ili reka koju sastavnice formiraju, morski sliv, ukupna dužina, fotografija |
| Planine | Referentni vrh PSS; promašaj dobija pin, km i smer | Vrh, visina, dokumentovana površina ili geografski region, fotografija |

Mapa i fotografije imaju zasebno uvećanje koje ne šalje odgovor. Unos je iznad svih šest numerisanih pokušaja. Kod reka se ne prikazuje proizvoljna udaljenost između „centara reka“: porede se označeni tokovi. Dekorativna pozadina sa imenima reka isključena je na toj igri.

Planine se prikazuju dokumentovanim referentnim vrhom. Ne postoji izmišljena precizna granica masiva. Za prekogranične planine naveden je obuhvat vrha; površina se koristi samo uz potvrđen izvor i obuhvat.

## Bodovanje i rang lista

Glavno pogađanje: 60, 54, 48, 42, 36 ili 30 poena za prvi do šestog pokušaja. Otkriven ili nepogođen odgovor daje 0. Pomoć oduzima 10 iz ove komponente, najmanje do 0. Svako tačno dodatno pitanje donosi 10, netačno/preskočeno 0. Izvor koji otkriva fotografiju može se otvoriti kroz označenu pomoć; tada aktivno pitanje daje 0.

Maksimum je **100 po igri, 500 dnevno ukupno**. Brzina nije kriterijum. Jedan upis po nalogu, datumu, sezoni i igri, uz serversku proveru vlasništva i verzije poteza. Nastavak na nalogu čuva i već odigrani deo. Gost ima nastavak u istom pregledaču; za javni plasman prijavom se povezuje započeta ili završena današnja partija sa nalogom, ako taj dnevni slot već nije popunjen.

Rang lista podrazumevano prikazuje samo dnevne zadatke nove sezone 4. Filteri: svih pet zajedno ili svaka igra posebno, dan/mesec/ukupno. Mesečni i ukupni pregled takođe sabiraju samo dnevne zadatke. Isti broj poena deli plasman. Ranije partije i rezultati ostaju sačuvani u istoriji/arhivi; vežbanje ne ulazi u novu javnu listu.

## Dve pomoći i vežbanje

Posle podešenog broja pokušaja (podrazumevano tri), prva pomoć otkriva početno slovo naziva. Druga prikazuje broj slova i prazna podvučena polja sa samo početnim slovom. Razmaci i crtice ne ulaze u broj slova; Lj, Nj i Dž su jedno slovo u srpskoj latinici. Administrativna odrednica u zagradi nije deo naziva za pomoć. Ukupan penal obe pomoći je ranije podešenih 10 poena, bez dodatnog uvećanja za drugu pomoć.

Vežbanje je ispod glavnog zadatka, ne pored oznake „Igra dana”. Može se igrati više puta. Dnevna partija, pokušaji, pomoći i rezultat ostaju sačuvani pri promeni režima. Vežbanje beleži lični napredak i poene, ali ne ulazi u javnu rang listu.

Administratorski pregled rang liste sadrži obnovu propuštenih upisa po nalogu i datumu. Koriste se samo završene dnevne partije iz serverske istorije: proveravaju se vlasnik, sezona, pravila, pokušaji i pitanja, a svi delovi bodovanja se ponovo računaju i porede. Anonimne partije i nekonzistentni podaci se ne dodeljuju nalogu. Postojeći dnevni slotovi se ne prepisuju, a postavka skrivanja sa rang liste ostaje poštovana.

## Katalog i uređivanje

Javni katalog: **255 naselja, 63 potvrđene reke, 188 planina**. Dodatnih 412 OSM naziva reka ostaje privatni nacrt do nezavisne provere. Dnevni fond trenutno koristi **226 naselja, 61 reku i 52 planine** sa kompletnim pitanjima i fotografijama. Širi, potvrđeni katalog služi za odgovore i bazu znanja. Ovo nije tvrdnja da je obrađena svaka reka u Srbiji.

Obuhvaćeni su Kosovo i Metohija. Popis RZS 2022. tamo nije sproveden: nepoznati broj stanovnika nije zamenjen izmišljenom vrednošću, već geografskim pitanjem. Kod drugih naselja koristi se stanovništvo samog naselja, ne cele opštine. Istoimena mesta imaju dodatnu administrativnu odrednicu.

WP → **Srbija na dlanu → Geografski katalozi**: pregled spremnosti, pretraga, pojedinačna izmena naziva/prevoda/alternativnih naziva, koordinata, činjenica, izvora i fotografije sa licencom; validirani JSON uvoz i izvoz. Postojeća podešavanja imaju aktivne igre, dnevni režim, uključene dodatne runde i bodovanje. Promena kataloga ne prepisuje već zamrznute zadatke tog dana. Analitika, igrači i istorija podržavaju svih pet vrsta.

Fotografije su lokalno u instalacionom ZIP-u. Manifest čuva HTTPS adresu, veličinu i SHA256; autor, licenca i izvor nalaze se uz zapis. Autori/licence dostupni su i pre odgovora, dok se izvor koji otkriva rešenje otvara kao pomoć. U toku igre nema zavisnosti od spoljnog servisa za mape ili slike.

[Izvori i ograničenja](../srbija-na-dlanu/EXPLORE-SOURCES.md) · [Provere](PROVERA.md)

[Provera naziva i svih 197 opštinskih teritorija](KATALOG-4.1.md). Kategorije baze znanja su centrirane, bez ivica; filteri rang liste su u levoj koloni, rezultati desno.
