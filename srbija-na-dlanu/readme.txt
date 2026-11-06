=== Srbija na dlanu ===
Contributors: srbija-na-dlanu
Tags: geography, quiz, serbia, education
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Dnevna geografska igra i vežbanje na jednoj WordPress stranici.

== Opis ==

Jedan dodatak, jedna stranica, dve odvojene igre: Okruzi i Opštine i gradovi.

* 29 upravnih okruga i Grad Beograd (30 celina).
* 174 opštine i grada, sa Kosovom i Metohijom, bez zasebnih gradskih opština.
* Šest pokušaja za prepoznavanje siluete; udaljenost i smer između geometrijskih centara.
* Pomoć posle trećeg promašaja.
* Tri dodatna pitanja: stanovništvo ili orijentacija, administrativna pripadnost ili sedište, znamenitost ili prostorni izazov.
* 30 fotografija znamenitosti sa autorima, licencama i izvorima.
* Dnevni izazov, vežbanje, nastavak partije, lokalni rezultati i deljenje bez otkrivanja rešenja.
* Pretraga prihvata srpsku latinicu, ćirilicu i unos bez kvačica.
* Sadržaj se uređuje kroz WordPress administraciju, sa JSON uvozom i izvozom.

PHP, WordPress options/transients, REST API, Media Library, JavaScript i CSS. Nema dodatnog servera, npm instalacije, API ključeva ili obaveznih dodataka.

== Instalacija ==

1. Dodaci > Dodaj novi > Otpremi dodatak: izaberi srbija-na-dlanu.zip.
2. Instaliraj i aktiviraj.
3. Napravi jednu stranicu i dodaj Shortcode blok: [srbija_na_dlanu]
4. Objavi stranicu. Preporučen je šablon pune širine.
5. Sadržaj uređuj u meniju Srbija na dlanu.

Za Elementor koristi Shortcode widget. Za klasični editor upiši isti shortcode u sadržaj stranice. Ne lepi PHP u editor.

== Podaci ==

Kosovo i Metohija su uključeni prema podeli na pet okruga i 29 opština. Za taj deo baze nema izmišljenih vrednosti Popisa 2022. Umesto stanovništva dobija se pitanje o orijentaciji, a umesto potencijalno nejasnog sedišta okruga pitanje o pripadnosti opština.

Osnovne granice: geoBoundaries / OpenStreetMap, 2017, ODbL 1.0. Dopunske granice Kosova i Metohije: javni geografski servis Ministarstva rudarstva i energetike. Geometrija je pojednostavljena i služi igri, ne katastarskoj ili službenoj upotrebi. Pojedinačni izvori i licence fotografija nalaze se u SOURCES.md.

== Dnevna igra i čuvanje ==

Dnevni izazov menja se u ponoć po vremenskoj zoni Europe/Belgrade, nezavisno od vremenske zone servera. Teritorija, pitanja i izbori su isti za sve. Izazov se čuva u WP options i ne menja se usred dana posle izmene sadržaja.

Aktivna partija se čuva u WordPress transient-u do tri dana. Pregledač čuva ključ te partije, kolekciju i niz završenih dana. Niz i rezultati su odvojeni za okruge i opštine. Brisanje podataka pregledača briše lokalnu statistiku. Nema sinhronizacije među uređajima, naloga ni javne rang-liste.

Osnovne stranice mogu da koriste keš. Isključi iz keširanja REST rute srbija-na-dlanu/v1/* i omogući JSON POST zahteve. Dodatak već vraća no-store zaglavlja. Ne blokiraj WordPress REST API.

Ograničenje javnih zahteva koristi privremeni HMAC mrežne adrese; sirova IP adresa se ne čuva u podacima dodatka. Standardni serverski pristupni logovi zavise od tvog hostinga.

== Napomene ==

WordPress tema treba da poziva wp_head() i wp_footer(). Ako tema ima usku kolonu, uključi šablon pune širine. Deaktivacija ne briše sadržaj i rezultate sa servera. JSON izvoz je rezervna kopija baze; fotografije dodate kroz Media Library prenose se uz standardnu WordPress rezervnu kopiju.

Ova verzija ima jednu fotografiju po okrugu i po odgovarajućoj opštini/gradu. Za ostale opštine poslednja runda koristi prostorno pitanje. Više fotografija po istoj teritoriji nije deo verzije 1.0.
