# Katalozi i navigacija — 4.1.0

Početna ima pet jednakih kartica u dve kolone i tri reda, sa jednom kolonom na telefonu. Uklonjeni su brojači „unosa u bazi“. Ispod igara je opis aplikacije, zatim slikovna pravila i ulaz u bazu znanja. Meni „Igre“ otvara svih pet igara; radi tastaturom i bez JavaScript-a.

Rang lista ima filtere levo i rezultate desno. Na manjem ekranu filteri idu iznad tabele. Ostaju dnevni, mesečni i ukupni pregled, zajednički i za svaku igru. Bodovanje, sezona 4 i sačuvani rezultati nisu resetovani.

Baza znanja zadržava mapu i spisak ispod. Kategorije su centrirane sa ivicom 1 px: okruzi, opštine, naselja, reke i planine. Liste imaju pretragu i sortiranje; reke i filter morskog sliva, opštine izbor matičnog grada, planine geografsku oblast. Glavna strana dodatno nudi pregled reka i planina. Detalji prikazuju pojedinačni izvor potvrde naziva.

## Broj opština i stvarne granice

[Zvanična podela RZS](https://stat.gov.rs/sr-latn/oblasti/registar-prostornih-jedinica-i-gis/administrativno-teritorijalna-podela-i-nstj-nivoi-1-2-3/) razlikuje **174 jedinice lokalne samouprave** (145 opština i 29 gradova) od **197 opština, gradskih opština i gradova** u prostornom registru. Pet gradova ima ukupno 28 gradskih opština: 174 − 5 + 28 = 197. Igra koristi drugi, detaljniji nivo, bez dupliranja matičnih gradova.

Ranijih 190 zapisa već je razdvajalo Beograd. Dopunjeni su Niš (5), Požarevac (2), Vranje (2) i Užice (2): uklonjene su četiri zbirne teritorije iz nove igre, dodato 11 gradskih opština. Njihovi stari identifikatori ostaju za istoriju; stare veze vode na odgovarajuće sastavne opštine. Svih 197 zapisa jedan-na-jedan odgovara šiframa iz [tekućeg šifarnika RZS](https://stat.gov.rs/media/412350/sifarnikgradovi-opstine-tekucestanje.xlsx). Referentni spisak je u `tests/fixtures/rzs-municipalities-2026.json`.

Stanovništvo je iz [RZS Popisa 2022](https://popis2022.stat.gov.rs/media/31323/0_ukupan-broj-stanovnika.xlsx). Zbir dece svake gradske celine odgovara ranijem zbiru; ukupno ostaje 6.647.003 u popisanom obuhvatu. Kosovo i Metohija ostaju uključeni kroz pet okruga i 29 opština, bez pripisivanja nepostojećeg Popisa RZS 2022.

Granice Niša su iz [MRE sloja 4](https://kro.mre.gov.rs/arcgis/rest/services/IMIS/MINING_PUBLIC/MapServer/4). Granice Kostolca, Sevojna i Vranjske Banje koriste OSM administrativne relacije nivoa 8: [6665948](https://www.openstreetmap.org/relation/6665948), [8031757](https://www.openstreetmap.org/relation/8031757), [10275950](https://www.openstreetmap.org/relation/10275950). Za svaki grad ta teritorija presečena je postojećom granicom matičnog grada; ostatak je centralna gradska opština. Provereno je da zbir nema preklapanja ni praznina. Obrisi su pojednostavljeni sa tolerancijom 40 m u UTM34N za igru; nisu katastarske karte.

## Provera prirodnih objekata

- Svih **188 planina** povezano je po nazivu i visini sa [pregledom Planinarskog saveza Srbije](https://pss.rs/planine-srbije/). U nepotpunim zapisima uklonjene su pogrešne veze i prevodi istoimenih stranih objekata. Geometrija je referentni vrh, ne izmišljena granica masiva.
- Javni katalog reka ima **63 potvrđena vodotoka**: 61 sa potpunim dnevnim pitanjima i dve potvrđene istoimene Toplice bez kompletnog foto-zadatka. Identiteti su provereni u [arhivskom pravilniku Republičke direkcije za vode](https://arhiva.rdvode.gov.rs/doc/dokumenta/podzak/Pravilnik%20o%20utvrdjivanju%20vodnih%20tela%20povrsinskih%20i%20podzemnih%20voda.pdf) ili konkretnom enciklopedijskom članku. Arhivski pravilnik je iz 2010, nije predstavljen kao tekući registar iz 2023. Jegrička i Nadela su stvarni, značajno izmenjeni vodotokovi.
- **412 nepotvrđenih OSM naziva** ostaje u privatnom uredničkom nacrtu. Primer „Agla“ nije proglašen nepostojećim: nema dovoljne nezavisne potvrde za javnu igru. Ne pojavljuje se ni u javnoj bazi, novim odgovorima, niti u ponuđenim odgovorima već započete dnevne igre.

Politika potvrde (`review_status`, `verification_source`, datum i beleška) uređuje se u **WP → Srbija na dlanu → Geografski katalozi**. Urednik vidi i nacrte i isključene stavke. Potvrda zahteva HTTPS izvor. Stariji JSON uvoz i ranije sačuvani katalozi nasleđuju proveru samo za isti identifikator, vrstu i naziv; nepoznati zapisi ostaju privatni.

Današnje zamrznuto pitanje i odigrani potezi ostaju isti. Nepotvrđene ponuđene reke uklanjaju se iz javnog prikaza i odbijaju bez trošenja pokušaja. U neodgovorenim starim pitanjima o reci uz naselje menjaju se samo nepotvrđeni ometači, uz iste identifikatore odgovora. Sledeći dnevni izbor koristi dopunjeni fond. Dnevni fond naselja/reka/planina ostaje 226/61/52; fotografije i njihove licence nisu menjane.
