# Interfejs i rang lista 4.2.0

Dugmad koriste zajedničku geometriju: 16 px tekst, najmanje 44 px visina, 12 px zaobljenje. Primarna dugmad su Sunrise sa tamnozelenim tekstom u tamnoj temi, svetlozelena sa tamnim tekstom u svetloj. Sekundarna su zelena sa belim tekstom. Izbor teme prati istu semantiku; svetla tema nema Sunrise. Kontrast podrazumevanog tamnog teksta na Sunrise podlozi je veći od 10:1. Ivica nema; fokus tastature zadržava jasno označen položaj.

Peta igra je centrirana. Igre, opis i slikovna pravila koriste istu maksimalnu širinu. Svetliji opis ima tamni tekst i veće unutrašnje razmake. Završetak početne u 4.2.1 ima dve razdvojene sekcije: veliki blok baze znanja sa originalnom SVG ilustracijom levo i pozivom desno, a zatim manji centriran blok naloga sa tri konkretne prednosti. Na telefonu ilustracija ide iznad teksta. SVG koristi postojeće granice svih 30 okruga, sedam stvarnih reka i četiri planinska simbola na referentnim položajima. Kod i izvor ilustracije su `tools/build-library-art.py` i `assets/library-atlas.svg`.

Za usmerenje rasporeda pregledane su početne [Komoot](https://www.komoot.com/) i [AllTrails](https://www.alltrails.com/): istraživanje uz vizuelni prikaz i zaseban poziv koji objašnjava prednosti naloga. Ilustracija, tekst i implementacija napravljeni su posebno za ovu aplikaciju, u njenoj postojećoj paleti. Meni „Igre” samo otvara pet izbora. Stari link `/igre/` preusmerava na početnu.

Igra dana je podrazumevana; vežbanje je zaseban izbor ispod nje. Dve pomoći otkrivaju početno slovo, pa broj slova i prazna polja. Puna reč se ne šalje kroz pomoć. Serverski brojač nivoa sprečava treću pomoć i čuva iste podatke pri nastavku partije.

Ispravljena je veza preuzete gostujuće partije sa rangiranjem: vlasništvo, rezervacija jednog dnevnog slota i stvarno čuvanje poena proveravaju se na serveru. Nastavak završene partije pokušava nedostajući upis bez dupliranja ličnog napretka. Rezultat vodi na listu datuma kada je partija odigrana.

Nova administratorska obnova koristi samo postojeće serverske zapise: proverava vlasništvo, datum, dnevni režim, sezonu 4, verziju 2 bodovanja, broj pokušaja/pitanja i ponovno izračunate komponente. Ne dodeljuje anonimne partije nalogu i poštuje skriven profil. Ponovljeni poziv ne duplira poene. REST i obrazac traže administratorsku dozvolu i važeći nonce.

Izvori geografije, fotografije, postojeći dnevni zadaci, sezona i težine bodovanja ostaju očuvani. Testovi su u `tests/interface42.php` i `tests/interface42-browser.cjs`, uz prethodne regresije svih pet igara.
