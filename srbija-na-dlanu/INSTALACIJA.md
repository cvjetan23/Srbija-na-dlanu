# Srbija na dlanu — WordPress dodatak

## Postavljanje na jednu stranicu

1. U WordPress-u otvori **Dodaci → Dodaj novi → Otpremi dodatak**.
2. Izaberi **srbija-na-dlanu.zip**, instaliraj i aktiviraj.
3. Otvori **Stranice → Dodaj novu** i nazovi je, na primer, „Srbija na dlanu“.
4. Dodaj blok **Shortcode** i u njega upiši:

   ```text
   [srbija_na_dlanu]
   ```

5. Izaberi šablon stranice **pune širine** ako ga tema ima i objavi stranicu.

Elementor: isti tekst upiši u **Shortcode widget**. Klasični editor: upiši shortcode u sadržaj. Nije potrebno dodavati PHP u stranicu niti menjati temu.

## Šta dobijaš

- Dve odvojene igre: **Okruzi** i **Opštine i gradovi**.
- **30 celina za okruge:** 29 upravnih okruga i Grad Beograd kao posebna celina.
- **174 opštine i grada**, uključujući **Kosovo i Metohiju**, bez dodatnih gradskih opština.
- Stvarne, pojednostavljene siluete, sever nagore, šest pokušaja, smer i vazdušna udaljenost između geometrijskih centara.
- Pomoć dostupna posle tri pogrešna pokušaja, označena u rezultatu.
- Tri dodatna pitanja, koja možeš igrati i kada ne pogodiš teritoriju.
- **30 fotografija znamenitosti**, sa autorima i licencama. Kod opština bez fotografije koristi se prostorno pitanje.
- Zajednički dnevni izazov, nasumično vežbanje, nastavak partije i deljenje bez rešenja.
- Lokalna kolekcija upoznatih teritorija i odvojeni nizovi odigranih dnevnih partija.
- Prilagođen prikaz za telefon; pretraga sa latinicom, ćirilicom i bez kvačica.

## Uređivanje sadržaja

U levom WP meniju pojaviće se **Srbija na dlanu**. Klikom na naziv teritorije možeš promeniti naziv, alternativne nazive, pomoć, stanovništvo sa godinom i izvorom, sedište, fotografiju i zanimljivost.

Fotografiju biraš iz WordPress biblioteke. Upiši naziv objekta, autora, licencu i HTTPS stranicu izvora. Za teritorije bez fotografije igra automatski postavlja prostorno pitanje. Imena i podaci se prikazuju kao tekst; SVG kod i skripte se ne prihvataju.

**Današnji izazov ostaje isti posle izmene sadržaja.** Izmene odmah važe za nove partije vežbanja i za buduće dnevne izazove.

## Stanovništvo i Kosovo i Metohija

Za stanovništvo je korišćena zvanična tabela RZS: **Popis 2022, ukupan broj stanovnika po opštinama i gradovima**. Brojevi se odnose na celu administrativnu teritoriju. Kod gradova sa gradskim opštinama uzet je ukupan broj za grad.

Kosovo i Metohija su u obe igre, sa **pet okruga i 29 opština** prema podeli korišćenoj u projektu. Pošto Popis RZS 2022. tamo nije sproveden, pitanje o stanovništvu zamenjuje se pitanjem o orijentaciji. Za pet okruga pitanje o sedištu zamenjuje se pitanjem o pripadnosti opština.

## Rezervna kopija i uvoz

Na dnu administratorske stranice klikni **Preuzmi JSON rezervnu kopiju**. Izvoz sadrži sve granice i trenutno uređene podatke.

Uvoz JSON datoteke zamenjuje bazu i prethodne izmene; najpre preuzmi rezervnu kopiju. Uvoz proverava identifikatore, geometriju, koordinate, povezanost sa okruzima, izvore i broj aktivnih teritorija. Objavljeni dnevni izazov ostaje sačuvan.

Fotografije iz ZIP-a dolaze uz dodatak. Za fotografije koje naknadno dodaješ u biblioteku koristi i uobičajenu WordPress rezervnu kopiju direktorijuma uploads.

## Tehnički zahtevi

- WordPress **6.2+** i PHP **7.4+**; preporučena je podržana verzija PHP-a.
- Omogućen WordPress REST API i JavaScript u pregledaču.
- Tema koja standardno poziva `wp_head()` i `wp_footer()`.
- Nisu potrebni poseban backend, nova baza, API ključevi, Node.js ili drugi dodaci.

Dodatak koristi WordPress options, transients, REST API, administratorske dozvole, nonce zaštitu i biblioteku medija. Baza teritorija je uključena u paket. Posle instalacije nisu potrebni spoljašnji servisi za granice ili pitanja. Ugrađene fotografije su lokalne; eksterni linkovi služe za izvore.

## Ako nešto ne radi

- **Vidi se tekst shortcode-a:** proveri da li je dodatak aktivan i da li koristiš Shortcode blok/widget.
- **Igra ne može da se učita:** proveri da sigurnosni dodatak ili hosting ne blokiraju `/wp-json/srbija-na-dlanu/v1/` i JSON POST zahteve.
- **Stari datum ili ista partija:** isključi REST rute igre iz keširanja i očisti keš dodataka/CDN-a. HTML stranica može da ostane keširana.
- **Nema stilova ili interakcije:** proveri `wp_head()` / `wp_footer()` i odloženo učitavanje/objedinjavanje JavaScript-a u optimizacionim dodacima.
- **Uska igra:** izaberi šablon stranice pune širine i ukloni bočnu traku.
- **Nema nastavka ili statistike:** privatni režim ili podešavanja pregledača mogu blokirati localStorage. Sama igra radi, ali čuvanje tada nije dostupno.

## Granice provere

Backend i shortcode su provereni na čistom WordPress-u 7.1.4, PHP-u 8.4.27 i SQLite bazi kroz WordPress-ov standardni `wpdb` interfejs. Provereni su sadržaj svih 204 teritorije, tok partije, pomoć, ponovljeni i nevažeći potezi, zaključavanje odgovora, nastavak, dnevna nepromenljivost i validacija uvoza. Nije rađena provera u tvojoj temi, na tvom hostingu ili u pravom pregledaču; za vizuelnu proveru okruženje nije imalo dostupan alat.

Nema javne rang-liste i zaštite namenjene nagradnim takmičenjima. Rezultati su za ličnu igru i poređenje sa prijateljima. Jedna fotografija po teritoriji je početna banka sadržaja; paket još ne rotira više fotografija iste teritorije.

Izvori i licence: **SOURCES.md** i **DATA-LICENSE.md** u dodatku.
