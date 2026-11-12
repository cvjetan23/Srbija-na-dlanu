=== Srbija na dlanu ===
Contributors: srbija-na-dlanu
Tags: geography, quiz, serbia, education
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 4.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Zasebna geografska aplikacija u WordPress-u: igre, baza znanja i nalozi.

== Opis ==

* Sopstveni kompas i meni, početna, Okruzi, Opštine, Baza znanja, Rang lista, Prijava i Moj nalog.
* Bez zaglavlja/podnožja teme na stranicama aplikacije; ostale stranice sajta zadržavaju temu.
* Sveden prikaz sa većim slovima: slika gore, pitanje/odgovori ispod.
* 29 upravnih okruga i Grad Beograd, 197 opština, gradskih opština i gradova prema RZS (svih 28 gradskih opština u pet gradova), uključujući pet okruga i 29 opština Kosova i Metohije.
* Podesiv broj pokušaja i pomoć; podrazumevano šest pokušaja i pomoć posle trećeg promašaja.
* Četiri dodatna pitanja: stanovništvo/položaj, geografija, zastava i znamenitost/prostorni izazov.
* 52 lokalne zastave pokrivaju 17 sedišta okruga i 51 opštinu/grad (68 aktivnih teritorija). Za mesta bez lokalne zastave podrazumevano se pita potvrđena zastava sedišta ili drugog jasno imenovanog mesta.
* 30 lokalnih fotografija sa autorima i licencama.
* Interaktivna mapa Srbije, mapa okruga sa opštinama, detalj opštine, pretraga i spiskovi ispod mapa.
* Standardni WP korisnici, prijava, registracija Pretplatnika, postavljanje/promena lozinke preko WP e-pošte.
* Aktivna partija i progres na nalogu, nastavak na drugom uređaju; gostujuća statistika u pregledaču.
* Dnevni izazov, vežbanje, pretraga sa oba pisma, deljenje bez rešenja.
* Administracija podataka i zastava, JSON uvoz/izvoz, Media Library.

== Instalacija ==

1. Otpremi instalacioni ZIP kroz Dodaci > Dodaj novi > Otpremi dodatak.
2. Pri ažuriranju izaberi Zameni postojeći dodatak. Aktiviraj ako je potrebno.
3. Otvori administraciju; automatski se pripremaju stranice ispod /srbija-na-dlanu/.
4. Očisti keš. Proveri prijavu i dostavu WP e-pošte.

Detaljno: INSTALACIJA.md. Shortcode [srbija_na_dlanu] ostaje dostupan za druge stranice sa njihovom temom.

== Podaci i čuvanje ==

Popis 2022. odnosi se na cele teritorije opština/gradova. Za Kosovo i Metohiju ne izmišljaju se brojevi Popisa RZS 2022; umesto stanovništva koristi se geografsko pitanje.

Dnevni izazov menja se u ponoć Europe/Belgrade. Pripremljen izazov ostaje isti posle izmene sadržaja. Rezultat se računa na serveru. Prijavljenim korisnicima aktivna partija traje do 30 dana; ukupni rezultati ostaju u usermeta. Gosti imaju serversku partiju do tri dana i lokalnu statistiku. WP transient keš može ranije isteći. Nema javne rang-liste.

Iz keširanja izuzmi svih šest stranica aplikacije, REST srbija-na-dlanu/v1/* i admin-ajax.php?action=snd_session. Ne keširaj privatni HTML. REST i AJAX vraćaju no-store; omogući JSON POST i X-WP-Nonce.

Ograničavanje zahteva koristi privremeni HMAC mrežne adrese; sirova IP adresa se ne čuva u podacima dodatka. Serverski pristupni logovi zavise od hostinga. Lozinke i sesije koristi i čuva WordPress.

Detaljna analitika se od 3.0.0 čuva u dve tabele iste WP baze: {prefix}snd_games i {prefix}snd_events. Administrator može da isključi beleženje, izuzme goste i promeni rok čuvanja (podrazumevano 365 dana). Progres je odvojen od analitike i ostaje na nalogu. Samo administratori sa manage_options vide spisak igrača, istoriju i izvode. Podržani su standardni WP alati za izvoz i brisanje ličnih podataka. Broj posetilaca i aktivno vreme su procene; vreme partije uključuje pauze.

== Licence ==

Kod: GPL-2.0-or-later. Geografija/fotografije: SOURCES.md i DATA-LICENSE.md. Zastave: FLAGS.md. Za dopunske granice Kosova i Metohije javni geografski servis nije naveo posebnu licencu za redistribuciju. Karte služe igri. Deaktivacija ne briše sadržaj i rezultate.

== Changelog ==

= 4.2.0 =
* Kompaktna dugmad bez ivica; primarne i sekundarne akcije, Sunrise samo u tamnoj temi.
* Poravnate sekcije početne, centrirana poslednja igra i odvojene sekcije za bazu i nalog.
* Dve pomoći za naziv: početno slovo, pa broj slova i prazna polja.
* Vraćeno vežbanje ispod igre; javni plasman ostaje samo za igre dana.
* Preuzimanje dnevne partije prijavom sada upisuje bodove; proverena obnova nedostajućih istorijskih rezultata.


= 4.1.2 =
* Autoptimize čuva redosled svih stilova aplikacije, uključujući nove mape i kataloge; ostale stranice sajta zadržavaju optimizaciju.

= 4.1.1 =
* Duži natpisi i dugmad završenih igara staju na uske ekrane u sva četiri jezika.

= 4.1.0 =
* Preglednija početna bez brojača unosa, opis i padajući izbor svih pet igara.
* Rang lista sa filterima levo i rezultatima desno; centrirane kategorije baze znanja sa tanjim ivicama.
* 197 teritorija usklađenih sa RZS, uključujući Niš, Požarevac, Vranje i Užice.
* Potvrđeni javni nazivi reka/planina, privatni nacrti i urednički izvori provere.
* Pretraga, sortiranje i filteri mapa/tabela za opštine, naselja, reke i planine.
* Očuvane započete partije, istorija i bodovanje.

= 4.0.0 =
* Pet dnevnih igara: okruzi, opštine, gradovi i naselja, reke i planine.
* Stvarne mape, numerisani pokušaji, četiri dodatna pitanja i lokalne fotografije sa autorima i licencama.
* Zajednička rang lista do 500 poena dnevno, pet filtera i nova sezona bez rezultata vežbanja.
* WP geografski katalog: uređivanje, izvori, fotografije, uvoz/izvoz i proširena baza znanja.
* Četiri jezika, obe teme, nalog i analitika za svih pet igara.


= 3.8.1 =
Kompaktniji jezik i tema; na manjim ekranima izbor jezika je levo, ikonice teme desno, a meni počinje od leve ivice.

= 3.8.0 =
* Isti stil glavnih dugmadi, filtera, formulara i akcija igre: zelena podloga, beli tekst, zajednički font, visina i radijus.
* Netačan izabrani odgovor je Sunrise, a tačan zelen, uz iks/kvačicu i čitljiv tekst u obe teme.
* Lupica je preko slike zastave, bez vidljivog teksta; uvećanje ostaje zasebna radnja sa pristupačnim nazivom i vraćanjem fokusa.

= 3.7.0 =
* Nova paleta Šuma i sunce: borova, šumska i žad zelena, topla svetla podloga i Sunrise akcent.
* Jasnije površine i kontrole, beliji natpisi, mirnije ivice, diskretnije reke i uređena dugmad Igraj.
* Vrati predložene boje u WP podešavanjima, bez promene pravila, registracije ili bodovanja.
* Očuvanje prilagođene palete i osvežavanje ranijih podrazumevanih kombinacija.

= 3.6.0 =
* Sedam osnovnih boja glavnog sajta uz Sunrise #FFD447; ravna dugmad sa belim natpisima i čitljivim kontrastom.
* Sunrise strelice i okvir tačnog odgovora bez dodatne zelene pozadine; objedinjene boje tabela, polja i mapa.
* Manje oznake pokušaja i zvezdica, posebno na opštinama; sitne teritorije imaju pomerene oznake sa veznom linijom. Veličina se prilagođava ekranu.
* Pozadina reka pokriva ceo ekran i pri skrolovanju, u boji iz zajedničke palete.
* WP podešavanja za sedam boja i dva dodatna izbora tamne teme. Postojeće prilagođene boje i pravila partija ostaju sačuvani.

= 3.5.1 =
* Više prostora za naziv meseca i godinu u nativnom filteru rang liste na uskom ekranu.


= 3.5.0 =
* Bodovanje 0–100 na serveru, zamrznuta pravila i zaštita od ponovljenog upisa.
* Odvojene liste Dnevni izazovi i Opšta rang lista; dnevni, mesečni i ukupni pregled, zajedno i po igrama.
* Javni nadimak i skrivanje plasmana na nalogu, administracija i CSV izvoz bodova, WP privatnost.
* Kružni kompas sa pozadinom i slojem preko cele stranice; kompaktnija slikovna pravila.
* Beli natpisi dugmadi u tamnoj temi, diskretni žuti akcenti #FFD447 i SVG reka iz Natural Earth podataka.
* Stvarna runda zastava i kada lokalna zastava nije potvrđena, uz jasno imenovano mesto i uvećanje.


= 3.4.0 =
* Istaknuta dugmad Igraj i ilustrovana pravila pre baze znanja.
* Unos iznad pokušaja, svetliji tekst i jasno istaknuta zadana teritorija.
* Uvećanje svake zastave bez slanja odgovora, pristupačan prozor i uredne SVG oznake.
* Svih 17 beogradskih gradskih opština: granice MRE i Popis RZS 2022, bez dvostrukog brojanja stanovništva.
* Sačuvane započete/dnevne partije, istorija i stari identifikator Beograda; nove partije koriste 190 teritorija.

= 3.3.0 =
* Stalno vidljiva numerisana polja i brojevi svih pokušaja na završnoj mapi.
* Ispravljeno zaostalo stanje učitavanja, posvetljivanje i poravnanje unosa/dugmeta.
* Ikonica naloga desno, kompaktan donji red i SVG ikonice akcija/smerova.
* Neutralni ponuđeni nazivi znamenitosti na četiri jezika, uz nove administratorske opcije.
* Puni nazivi, postojeći identifikatori odgovora i započete dnevne partije ostaju očuvani.

= 3.2.0 =
* Tabele pripadajućih opština: naziv, stanovništvo i godina popisa, sa jasnom oznakom nedostupnih podataka.
* Ravan izbor jezika i ikonice teme bez dodatnih vizuelnih naslova.
* Kompas-loader, blage animacije teksta i teme, i podrška smanjenom kretanju.
* Rezervni WP AJAX kanal za prekide REST veze; očuvane provere nonce-a, vlasništva i revizije.
* Idempotentan početak partije i oporavak izgubljenog odgovora bez dupliranja poteza/rezultata.
* Same-origin referrer pravilo omogućava stvarnu browser prijavu uz postojeću proveru Origin-a i nonce-a.


= 3.1.0 =
* Pregledniji izbor jezika sa punim nazivima i obeleženim aktivnim jezikom.
* Sunce, mesec i automatska tema; dostupno tastaturi, dodiru i bez JavaScript-a.
* Baza znanja: mapa svih okruga Srbije, zatim mapa izabranog okruga sa opštinama.
* Klik na opštinu otvara njen obris, podatke, izvore i položaj u okrugu.
* Postojeći spiskovi ostaju ispod mapa, sa direktnim linkovima na sve detalje.
* Svi okruzi i 174 opštine/grada, uključujući Kosovo i Metohiju, ostaju sačuvani.


= 3.0.0 =
* Svetla, tamna i automatska tema; četiri jezika: srpski, engleski, nemački i ruski.
* Prevedena pitanja, objašnjenja, znamenitosti, prijava, poruke za lozinku, baza znanja i deljenje.
* Administrativna podešavanja pravila, izgleda, jezika, registracije i analitike; urednik prevoda.
* Spisak igrača, detaljna istorija pokušaja i kviza, filteri, pregledi i CSV/JSON izvoz.
* Uvoz dostupnih starijih dnevnih rezultata kao zbirnih zapisa; ne postoje izmišljeni detalji starih partija.
* Lokalna analitika bez spoljnih servisa, podesiv rok čuvanja, WP izvoz/brisanje ličnih podataka.
* Ispravljeno spajanje parametara u registracionom linku za lozinku i zaglavlja kod tema bez title-tag podrške.


= 2.1.0 =
* Ispravke nalaza iz dva QA izveštaja: deljenje otvara traženi režim, a stari link jasno vodi na današnji izazov.
* Pretraga: prvi ArrowUp bira poslednju stavku, dodirno skrolovanje ne bira odgovor, ponovljeni pokušaj ima posebnu poruku.
* Rezime prikazuje pitanje, tvoj odgovor, tačan odgovor i sliku tačne zastave, i posle ponovnog otvaranja.
* Jasni ishodi pogođeno/otkriveno/nepogođeno, pomoć i niz igranja sa pravilnim oblikom dan/dana.
* Izvor fotografije pre odgovora označen je kao pomoć; autor/licenca ostaju vidljivi.
* Problem sa slikom nudi ponovno učitavanje ili serversko zamensko pitanje bez gubitka pokušaja.
* Geografska zamena više ne ocenjuje region iz pomoći; poboljšani pogrešni izbori i izvori geometrije.
* Odvojeni limiti po igraču, veći zajednički IP prag, pouzdaniji prelazak ponoći i oporavak sesije.
* Manji odgovori tokom kviza, izuzimanje resursa igre iz problematične minifikacije, uklanjanje tema/builder resursa na aplikaciji.
* Veći kontrast, jedno pristupačno ime opcije zastave i ispravljeni naslovi, opisi, canonical i jezik strukturiranih podataka.


= 2.0.0 =
* Zasebna aplikacija sa kompasom, sopstvenim menijem i šest WP stranica.
* WP prijava/registracija/lozinka i serverski progres po nalogu, sa nastavkom na drugom uređaju.
* Pretraživa baza svih okruga sa njihovim siluetama, detaljima i izvorima.
* Četvrta bonus runda: četiri slike zastava ili označeno zamensko geografsko pitanje.
* Veća slova, zaštita privatnih partija, upis rezultata jednom i zaključavanje istovremenih poteza.

= 1.1.0 =
* Sveden prikaz u jednoj koloni, slika gore i pitanje/odgovori ispod.

= 1.0.1 =
* Ispravljeno učitavanje CSS-a za Elementor i kasno renderovan shortcode.
