# Kod i izvori podataka

PHP, JavaScript i CSS ovog dodatka objavljeni su pod GPL-2.0-or-later. Licenca koda ne zamenjuje pojedinačne licence podataka i fotografija.

## Osnovni geografski sloj

Osnovne granice 25 celina i 145 opština/gradova preuzete su iz geoBoundaries gbOpen, SRB ADM1 i ADM2 (granice za 2017), izvor OpenStreetMap/Wambacher, © OpenStreetMap contributors. Ovaj sloj i njegovi izvedeni podaci podležu **Open Data Commons Open Database License 1.0**:

- https://opendatacommons.org/licenses/odbl/1-0/
- https://www.openstreetmap.org/copyright
- https://www.geoboundaries.org/

Geometrija je pojednostavljena u metričkoj projekciji; iz nje su izvedene SVG siluete i geometrijski centri. Izvedena baza dostupna je u `data/territories.php` i može se izvesti kroz administratorski JSON izvoz. Baza je odvojena od programskog koda. Oznaka ODbL ne predstavlja tvrdnju da izvor propisuje granice dopunskog sloja ispod.

## Dopunski sloj Kosova i Metohije

Granice pet okruga i 29 opština preuzete su iz javnog servisa Ministarstva rudarstva i energetike Republike Srbije, IMIS/MINING_PUBLIC, slojevi 4 i 5:

https://kro.mre.gov.rs/arcgis/rest/services/IMIS/MINING_PUBLIC/MapServer

Zadržana je podela koju objavljuje izvor; geometrija je pojednostavljena radi igre. Servis u preuzetim metapodacima nije naveo posebnu licencu za ponovno distribuiranje ovog sloja. Zato se za taj sloj ne tvrdi da je GPL ili ODbL; uslove daljeg distribuiranja i komercijalne upotrebe treba potvrditi kod izvora.

## Stanovništvo

Republički zavod za statistiku, Popis 2022, tabela „Ukupan broj stanovnika, po opštinama i gradovima“. Činjenične vrednosti preuzete su uz navođenje izvora i godine. Ne koriste se izmišljeni podaci za Kosovo i Metohiju.

https://popis2022.stat.gov.rs/media/31323/0_ukupan-broj-stanovnika.xlsx

## Fotografije

Fotografije imaju zasebne licence, autore i originalne stranice navedene u `SOURCES.md`. Obrada se sastoji od smanjenja rezolucije i JPEG kompresije za veb. Autori nisu povezani sa igrom niti je podržavaju samim ustupanjem fotografija pod otvorenim licencama.
