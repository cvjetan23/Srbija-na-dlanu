# Interfejs i rang lista 4.2.0

Dugmad koriste zajedničku geometriju: 16 px tekst, najmanje 44 px visina, 12 px zaobljenje. Primarna dugmad su Sunrise sa tamnozelenim tekstom u tamnoj temi, svetlozelena sa tamnim tekstom u svetloj. Sekundarna su zelena sa belim tekstom. Izbor teme prati istu semantiku; svetla tema nema Sunrise. Kontrast podrazumevanog tamnog teksta na Sunrise podlozi je veći od 10:1. Ivica nema; fokus tastature koristi pozadinu ili podvlačenje umesto okvira.

Peta igra je centrirana. Igre, opis i slikovna pravila koriste istu maksimalnu širinu. Svetliji opis ima tamni tekst i veće unutrašnje razmake. Završetak početne u 4.2.1 ima dve razdvojene sekcije: veliki blok baze znanja sa svedenom SVG ilustracijom na providnoj pozadini levo i pozivom desno, a zatim manji centriran blok naloga sa tri konkretne prednosti. Na telefonu ilustracija ide iznad teksta. SVG koristi postojeće granice svih 30 okruga, sedam stvarnih reka i četiri planinska simbola na referentnim položajima. Kod i izvor ilustracije su `tools/build-library-art.py` i `assets/library-atlas.svg`.

Za usmerenje rasporeda pregledane su početne [Komoot](https://www.komoot.com/) i [AllTrails](https://www.alltrails.com/): istraživanje uz vizuelni prikaz i zaseban poziv koji objašnjava prednosti naloga. Ilustracija, tekst i implementacija napravljeni su posebno za ovu aplikaciju, u njenoj postojećoj paleti. Meni „Igre” samo otvara pet izbora. Stari link `/igre/` preusmerava na početnu.

Igra dana je podrazumevana; vežbanje je zaseban izbor ispod nje. Dve pomoći otkrivaju početno slovo, pa broj slova i prazna polja. Puna reč se ne šalje kroz pomoć. Serverski brojač nivoa sprečava treću pomoć i čuva iste podatke pri nastavku partije.

Ispravljena je veza preuzete gostujuće partije sa rangiranjem: vlasništvo, rezervacija jednog dnevnog slota i stvarno čuvanje poena proveravaju se na serveru. Nastavak završene partije pokušava nedostajući upis bez dupliranja ličnog napretka. Rezultat vodi na listu datuma kada je partija odigrana.

Nova administratorska obnova koristi samo postojeće serverske zapise: proverava vlasništvo, datum, dnevni režim, sezonu 4, verziju 2 bodovanja, broj pokušaja/pitanja i ponovno izračunate komponente. Ne dodeljuje anonimne partije nalogu i poštuje skriven profil. Ponovljeni poziv ne duplira poene. REST i obrazac traže administratorsku dozvolu i važeći nonce.

Izvori geografije, fotografije, postojeći dnevni zadaci, sezona i težine bodovanja ostaju očuvani. Testovi su u `tests/interface42.php` i `tests/interface42-browser.cjs`, uz prethodne regresije svih pet igara.

U 4.2.3 odgovor se čuva bez prekrivanja ili zamućenja cele stranice. Onemogućene odgovorene opcije zadržavaju punu neprozirnost, a animacija bledenja posle svakog poteza je uklonjena. Prekrivač ostaje za prvo učitavanje igre i navigaciju. Hover čuva istu boju primarnog ili sekundarnog dugmeta i menja samo osvetljenje za četiri procenta.

„Izvor kao pomoć” uklonjen je iz interfejsa fotografija. Autor i licenca ostaju dostupni u sklopivim detaljima odmah ispod slike. Otvaranje tih detalja ne šalje potez niti oduzima poene. Prethodna istorija i serverska obrada već korišćene pomoći ostaju kompatibilne, kako se ne bi promenili raniji rezultati. SVG više nema podlogu ili ukrasne krugove; u aplikaciji preuzima boju linija iz izabrane teme.

U 4.2.4 javna baza znanja zahteva osnovne činjenice o rekama (ušće, morski sliv, pozitivna dužina) i planinama (vrh i visina). Dve potvrđene Toplice bez tih podataka ostaju u uredničkom katalogu, dok javni spisak i mapa prikazuju 61 potpun zapis. Stari link ka nepotpunom zapisu privremeno vodi na spisak; posle dopune može ponovo prikazati detalj. Poznati nazivi za odgovore i ranije zamrznute partije nisu prepravljeni. Sve 188 planine i 197 opštinskih teritorija ostaju dostupne; nepoznati uporedivi podaci Popisa 2022. za Kosovo i Metohiju nisu razlog za uklanjanje geografskih objekata. Filteri koriste pozadinu stranice unutar svojih jasno poravnatih polja, prilagođenih telefonu.

U 4.2.5 lupe na fotografijama koriste belu ikonicu na zelenoj podlozi; lupa mape ima tamnu ikonicu na primarnoj podlozi. Ulazna polja koriste različitu podlogu od kartice ili stranice na kojoj se nalaze, uključujući prijavu, javni nadimak, pretrage i datume. Dijalozi imaju jednobojnu podlogu teme, bez zamućivanja, sa mirnim tamnim prekrivačem.

Podrazumevana beogradska znamenitost je Hram Svetog Save na Vračaru. Fotografija Borise Živkovića (Borisha), javno dobro, proverena je na Wikimedia Commons i nalazi se lokalno u ZIP-u. Samo podrazumevani zapisi za Grad Beograd i Beograd dobijaju zamenu; Stari grad zadržava svoju geografsku znamenitost sa jasnim nazivom „Beogradska tvrđava (Kalemegdan)”. Administratorske izmene imaju prednost. Nove partije mogu da dobiju crkvu i iz ranije zamrznutog dana, preko zasebnog pitanja po sesiji. Započete partije i postojeći odgovori ostaju sa istim identifikatorima; stare fotografije nisu obrisane.
