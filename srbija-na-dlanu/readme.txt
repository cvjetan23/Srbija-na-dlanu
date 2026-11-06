=== Srbija na dlanu ===
Contributors: srbija-na-dlanu
Tags: geography, quiz, serbia, education
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Zasebna geografska aplikacija u WordPress-u: igre, baza znanja i nalozi.

== Opis ==

* Sopstveni kompas i meni, početna, Okruzi, Opštine, Baza znanja, Prijava i Moj nalog.
* Bez zaglavlja/podnožja teme na stranicama aplikacije; ostale stranice sajta zadržavaju temu.
* Sveden prikaz sa većim slovima: slika gore, pitanje/odgovori ispod.
* 29 upravnih okruga i Grad Beograd, 174 opštine/grada, uključujući pet okruga i 29 opština Kosova i Metohije.
* Šest pokušaja, udaljenost/smer i pomoć posle trećeg promašaja.
* Četiri dodatna pitanja: stanovništvo/položaj, geografija, zastava i znamenitost/prostorni izazov.
* 52 lokalne zastave pokrivaju 17 sedišta okruga i 52 opštine/grada (69 teritorija). Za mesta bez zastave koristi se označeno geografsko pitanje.
* 30 lokalnih fotografija sa autorima i licencama.
* Pretraga svih okruga, detalji i samo silueta izabranog okruga.
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

Iz keširanja izuzmi prijavu, nalog, REST srbija-na-dlanu/v1/* i admin-ajax.php?action=snd_session. Ne keširaj privatni HTML. REST i AJAX vraćaju no-store; omogući JSON POST i X-WP-Nonce.

Ograničavanje zahteva koristi privremeni HMAC mrežne adrese; sirova IP adresa se ne čuva u podacima dodatka. Serverski pristupni logovi zavise od hostinga. Lozinke i sesije koristi i čuva WordPress.

== Licence ==

Kod: GPL-2.0-or-later. Geografija/fotografije: SOURCES.md i DATA-LICENSE.md. Zastave: FLAGS.md. Za dopunske granice Kosova i Metohije javni geografski servis nije naveo posebnu licencu za redistribuciju. Karte služe igri. Deaktivacija ne briše sadržaj i rezultate.

== Changelog ==

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
