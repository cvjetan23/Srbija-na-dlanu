# Srbija na dlanu

Geografska igra za jednu WordPress stranicu: **Okruzi** i **Opštine i gradovi**, sa dnevnim izazovom i režimom vežbanja.

**[Preuzmi WordPress ZIP dodatak](https://github.com/cvjetan23/Srbija-na-dlanu/raw/refs/heads/main/dist/srbija-na-dlanu.zip)** · [Detaljno uputstvo](srbija-na-dlanu/INSTALACIJA.md) · [Provera](docs/PROVERA.md)

## Instalacija

1. Preuzmi `dist/srbija-na-dlanu.zip` preko linka iznad. GitHub opcija **Code → Download ZIP** preuzima ceo repozitorijum i nije instalacioni ZIP dodatka.
2. U WordPress-u otvori **Dodaci → Dodaj novi → Otpremi dodatak**. Otpremi instalacioni ZIP, instaliraj i aktiviraj dodatak.
3. Napravi jednu stranicu, dodaj **Shortcode** blok i upiši:

   ```text
   [srbija_na_dlanu]
   ```

4. Objavi stranicu. Preporučen je šablon pune širine. U Elementoru koristi Shortcode widget.

Sadržaj se uređuje kroz meni **Srbija na dlanu** u WordPress administraciji.

## Igra

- **30 celina**: 29 upravnih okruga i Grad Beograd kao posebna celina.
- **174 opštine i grada, uključujući Kosovo i Metohiju**, bez zasebnih gradskih opština.
- Stvarne siluete, šest pokušaja, vazdušna udaljenost i smer između geometrijskih centara.
- Pomoć posle trećeg promašaja i tri dodatna pitanja posle otkrivanja teritorije.
- **30 lokalnih fotografija znamenitosti**, sa autorima, licencama i izvorima; prostorno pitanje kada fotografija nije dostupna.
- Pretraga prihvata latinicu, ćirilicu i unos bez kvačica.
- Isti dnevni zadatak za sve, promena u ponoć po vremenu u Srbiji; odvojeno nasumično vežbanje.
- Nastavak partije, lokalna statistika i deljenje rezultata bez otkrivanja rešenja.

Kosovo i Metohija su uključeni sa **pet okruga i 29 opština** prema podeli iz projektne baze. Pošto Popis RZS 2022. tamo nije sproveden, pitanje o stanovništvu zamenjuje se geografskim pitanjem. Brojevi stanovnika na ostaloj teritoriji odnose se na cele opštine i gradove.

## Tehnologija

WordPress **6.2+**, PHP **7.4+**, nativni JavaScript i CSS. Backend koristi postojeću WordPress bazu kroz options i transients, REST API, administratorske dozvole, nonce zaštitu i Media Library. Nisu potrebni poseban server, dodatna baza, Node.js, API ključevi ili obavezni dodaci.

Stranica može da koristi keš, ali rute `/wp-json/srbija-na-dlanu/v1/*` treba izuzeti iz keširanja i dozvoliti JSON POST zahteve. Dodatak sam vraća `no-store` zaglavlja. Dnevni zadatak ostaje isti i ako administrator tokom dana promeni sadržaj.

## Struktura

```text
srbija-na-dlanu/       Izvorni kod WordPress dodatka i ugrađena baza
dist/                 Instalacioni ZIP i SHA-256 kontrolni zbir
docs/PROVERA.md        Izvršene provere i granice verifikacije
tools/build-package.py Ponovljivo pravljenje instalacionog ZIP-a
```

Za izradu ZIP-a iz izvornog koda dovoljan je Python 3:

```sh
python3 tools/build-package.py
```

## Provera i ograničenja

Backend i aktivacija dodatka provereni su na WordPress-u 7.1.4 i PHP-u 8.4.27, sa 2.222 provere. Popisni zbir opština i gradova usklađen je sa zvaničnih 6.647.003 stanovnika iz Popisa 2022. Detalji su u [izveštaju](docs/PROVERA.md).

Prikaz u pravom pregledaču, na konkretnom hostingu i u korisničkoj temi još nije testiran. Rezultati su lokalni, bez sinhronizacije među uređajima i bez javne rang-liste. Po teritoriji je podržana jedna fotografija znamenitosti.

## Izvori i licence

Kod: **GPL-2.0-or-later**. Fotografije i geografski slojevi imaju zasebne uslove korišćenja: [SOURCES.md](srbija-na-dlanu/SOURCES.md) i [DATA-LICENSE.md](srbija-na-dlanu/DATA-LICENSE.md).

Osnovne granice su iz geoBoundaries / OpenStreetMap pod ODbL 1.0. Dopunske granice Kosova i Metohije potiču iz javnog servisa Ministarstva rudarstva i energetike; taj servis nije naveo posebnu licencu za ponovno distribuiranje. Uslovi su opisani u DATA-LICENSE.md. Karte služe igri i nisu katastarske karte.
