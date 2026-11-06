# Mape i izbor prikaza — 3.1.0

Izbor jezika sada ima vidljivu oznaku, globus, pun naziv aktivnog jezika i meni sa sva četiri naziva. Aktivni jezik je označen. Escape zatvara izbor i vraća fokus, a klik izvan njega zatvara meni. Jezici koje administrator isključi ne prikazuju se.

Tema ima sunce za svetlu, mesec za tamnu i monitor za automatsku temu. Pritisnuto dugme ima vidljiv i pristupačan aktivni status. Izbor se pamti, a automatska tema prati promenu uređaja. Bez JavaScript-a koriste se standardni GET obrazac i kolačić; nema dodatnog servera ili biblioteke u produkciji.

Baza znanja ima tok **Srbija → okrug → opština**. Na početku je svih 30 okružnih celina na jednoj mapi. Svaki okrug otvara sopstvenu mapu sa granicama pripadajućih opština. Klik na opštinu otvara njen obris, podatke, izvore i istaknut položaj u okrugu. Spiskovi su ostali ispod mapa; njihovi linkovi vode na iste detalje.

Svi oblici potiču iz postojeće projektne geografske baze. Ostaju 29 upravnih okruga i Grad Beograd kao posebna celina, sa 174 opštine/grada. Kosovo i Metohija ostaju uključeni sa pet okruga i 29 opština. Gradske opštine Beograda nisu naknadno izmišljene ili dodate kao zasebne teritorije. Popisni podaci i napomene ostaju isti.

SVG oblasti su obični linkovi, dostupni i tastaturi. Fokus ili pokazivač prikazuju naziv oblasti. Spiskovi služe i kao alternativa preciznom izboru na mapi. Granice ugrađene baze se prilagođavaju okrugu već na serveru; JavaScript dodatno prilagođava uvezene SVG putanje. Promena jezika čuva izabranu opštinu/okrug i režim igre, bez prenošenja lozinke ili privatnog tokena u linkove.

Novi detalji opština koriste postojeću stranicu Baze znanja, npr. `?okrug=rs-17&opstina=m-cacak&lang=sr`. Canonical, prevodi, breadcrumb i podaci u zaglavlju odnose se na odabranu opštinu i njen pravi okrug. Nisu potrebne nove WP stranice ili API ključevi.

## Provera

- **6.216 novih WP tvrdnji** proveravaju svih 30 okružnih mapa i 174 detalja opština u sva četiri jezika, tačnu pripadnost i linkove, izdvojen obris, spiskove, metadata/breadcrumb, obuhvat Kosova i Metohije, podrazumevane permalinke i administratorska ograničenja izbora jezika/teme.
- **98 tvrdnji u pravom Chromium-u**: stvarni klik u unutrašnjost SVG oblasti, izbor okruga i opštine, zadržavanje detalja pri promeni jezika, tastatura/Escape, sunce/mesec/automatska tema, pamćenje i ponovno učitavanje, kao i normalno ponašanje bez JavaScript-a.
- Proverene su širine **320, 390, 768 i 1440 px**, dodirne akcije i oba izgleda. Nije pronađeno horizontalno prelivanje ili JavaScript greška. Pregledani su stvarni snimci Chromium-a. Fizički telefon nije korišćen.
- Ponovo se pokreću postojeći testovi igre, naloga, analitike i izvoza; nova promena ne resetuje progres ili dnevne izazove.

Ponovljivi testovi: `tests/atlas-flow.php`, `tests/atlas-browser.cjs`. Playwright Core i Chromium koriste se samo u testovima; nisu deo instalacionog ZIP-a. GitHub Actions pokreće testove na MySQL-u sa Autoptimize i Slim SEO i objavljuje snimke kao zaseban artefakt.
