# Pregled izdanja 3.6.0

Zadržana je stvarna Astra paleta javnog sajta cvjetan.dev, proverena 7. novembra 2026. Osnova ima sedam boja: maslinastu `#6F7C45`, tamnu `#171914`, površinu `#292C25`, svetlu `#EEF0E5`, belu, ivicu `#D8DCCB` i Sunrise `#FFD447`. SVG zastave i fotografije zadržavaju svoje izvorne boje.

| Element | Izmena i provera |
|---|---|
| Početna i Igraj | Ravna maslinasta dugmad, beli natpisi, Sunrise ivica/strelica u tamnoj temi; uklonjena donja traka. Kontrast belog natpisa prema podrazumevanoj maslinastoj je 4,52:1. Ispravljen automatski izbor natpisa koji je ranije mogao da odabere neodgovarajuću tamnu boju. |
| Reke | SVG alfa maska u boji zajedničke palete, preko cele visine i širine ekrana. Proverena širina 2560 px i skrolovanje, kao i mobilni prikaz. |
| Mapa pokušaja | Opštine: broj ima krug prečnika 12 px, zadata teritorija 16 px. Okruzi: 18/22 px. Veličina ostaje ista pri promeni širine; sitni oblici dobijaju pomerenu oznaku i veznu liniju. Svaki pokušaj ostaje numerisan na mapi i u legendi. |
| Pokušaji i pomoć | Unos ostaje iznad šest polja, Sunrise strelice u tamnoj temi. Nema posvetljivanja cele table nakon odgovora. |
| Tačan odgovor | Sunrise okvir i kružna SVG kvačica; pozadina ostaje ista kao kod ostalih opcija. Uklonjene posebne zelene nijanse uspeha. Netačan odgovor se razlikuje simbolom i tekstom. |
| Zastave | Sačuvana runda i četiri odvojena dugmeta za uvećanje. Pregled ne šalje odgovor; Escape/zatvaranje vraćaju fokus. |
| Tema i jezici | Sunce/mesec/uređaj i četiri jasno imenovana jezika. Aktivna tema ima Sunrise konturu. Provereni obični obrasci, tastatura i smanjene animacije. |
| Baza znanja | Objedinjene boje mapa, kartica i tabela, bez četiri različite zelene ispune. Granice, izabrana teritorija i interakcija ostaju vidljivi. |
| Rang liste i nalozi | Obe liste, periodi, filteri, bodovanje i privatnost ostaju sačuvani; provereni stvarni upisi isključivo u izdvojenom test WordPress-u. |
| Administracija | Podešavanja → Paleta boja: sedam osnovnih boja i dva dodatna izbora tamne teme. Nativni obrazac provereno menja javne pozadine, kartice, dugmad i akcent. Stare podrazumevane boje se osvežavaju; prilagođene vrednosti i pravila partija ostaju sačuvani. |

Provere izdvojene instalacije: **23** nove serverske tvrdnje o migraciji/validaciji/kontrastu, **141** JavaScript tvrdnja i **307** tvrdnji u stvarnom Chromium-u. Nova provera obuhvata male beogradske opštine i vezne linije na 320/768/1440 px, celu pozadinu na 2560 px, očuvanu rundu zastava, jednake površine odgovora i stvarno čuvanje boja kroz WP obrazac.

Kompletan GitHub Actions prolaz koristi WordPress 7.1.4, PHP 8.3.6, MySQL 8.0.46, Autoptimize i Slim SEO. Podaci, granice Kosova i Metohije, četiri dodatne runde, sačuvane partije i raniji bodovi nisu prepravljani. Produkcijska evidencija i njene granice navedene su u [LIVE-CHECK.md](LIVE-CHECK.md).
