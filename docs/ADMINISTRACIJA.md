# Administracija

Sve funkcije koriste postojeću WordPress bazu i WP korisnike. Ne postoji spoljašnji servis za analitiku. Pristup administraciji i izvozu zahteva `manage_options`; igrači ne vide tuđu istoriju.

| WP podmeni | Namena |
|---|---|
| Srbija na dlanu | Teritorije, stanovništvo, fotografije, lokalni prevodi zanimljivosti, zastave, izvori i JSON baze |
| Podešavanja | Naziv, dostupni/podrazumevani jezici, tema i izbor igrača, boje, osnovna veličina teksta, igre/režimi, pokušaji, pomoć, runde, registracija, analitika i rok čuvanja |
| Prevodi | Pretraga i izmena ugrađenih tekstova za srpski, engleski, nemački i ruski; provera oznaka i vraćanje ugrađenog prevoda |
| Igrači | Pretraga WP naloga po imenu/nalogu/emailu, datum registracije, dnevne partije, zbir vežbanja, kviz, upoznate teritorije i niz |
| Partije | Datum, igrač/gost, teritorija, faza, ishod, pokušaji, pomoć, kviz, jezik i tema; detalj sa stvarnim odgovorima i tokom partije |
| Analitika | Dnevni pregled, najviše igrane teritorije, uspeh, jezici, teme, uređaji po širini prikaza, domeni preporuke i greške API-ja |

Filteri partija: datum, igra, režim, faza, jezik i ID korisnika. Aktivnosti prate datum, jezik i korisnika; igra/režim/faza nisu atributi prikaza početne, baze znanja ili prijave. Izvoz igrača daje trenutno stanje naloga i progresa, nezavisno od datumskog filtera. Izvozi CSV/JSON sadrže samo podatke iz WP-a i lokalne analitike; CSV tekst koji bi mogao postati formula dobija zaštitni prefiks.

Broj pregleda je ograničen na jedan po istom posetiocu i stranici u pet minuta. Jedinstveni posetioci su procena prema HMAC identifikatoru naloga ili funkcionalnog kolačića gosta. Brisanje kolačića i prijava mogu promeniti tu procenu. Aktivno vreme se procenjuje periodičnim signalima dok je stranica vidljiva i u fokusu, uz serversko ograničavanje. Proteklo vreme partije obuhvata pauze i nije isto što i aktivno vreme. Nezavršena partija nije automatski proglašena odustajanjem.

Čuvaju se datum/vreme u UTC, datum izazova po Europe/Belgrade, rezultat, stvarni pokušaji i odgovori, upotreba pomoći, tehnička zamena slike, jezik, tema i osnovne aktivnosti. Preporuka čuva samo domen, a uređaj grubu kategoriju širine prikaza. Ne čuvaju se sirove IP adrese, sadržaj URL upita, lozinke, kolačići ili privatni tokeni partija u tabelama analitike. Sesije i lozinke održava WordPress; hosting može imati sopstvene pristupne logove.

Stariji dnevni rezultati uvoze se u grupama do 25 naloga preko WP-Cron-a i imaju oznaku starijeg zbirnog zapisa. Precizno vreme, izbori i pokušaji koji ranije nisu sačuvani ostaju nepoznati. Raniji zbir vežbanja prikazan je u progresu korisnika; detaljne stare partije se ne generišu iz tog broja.

Podrazumevano se detaljna analitika čuva 365 dana; podesivo 7–3650 ili 0 za neograničeno. Dnevno čišćenje ne briše progres na nalogu. Brisanje WP naloga podrazumevano briše njegovu analitiku; ako se to isključi, zapisi se anonimizuju. WP izvoz/brisanje ličnih podataka obuhvata igru. Ručno brisanje celokupne analitike zahteva administratorski nonce i posebno označenu potvrdu; stariji zbirni rezultati se zatim ne uvoze ponovo automatski.

Backup obuhvata WP `options`, `usermeta`, `{prefix}snd_games`, `{prefix}snd_events` i lokalne medije. JSON geografske baze obuhvata sadržaj i prevode znamenitosti, a ne korisničke naloge/analitiku. Deaktivacija čuva podatke.


Od verzije 3.3.0, uređivanje teritorije uključuje poseban naziv znamenitosti u ponuđenim odgovorima, na srpskom, engleskom, nemačkom i ruskom. Upiši naziv bez grada koji bi otkrio rešenje; puni zvanični naziv i objašnjenje ostaju odvojeni. Prazno polje koristi ugrađeni neutralni naziv, a nova prilagođena znamenitost njen zvanični naziv dok ne uneseš neutralni. Podatak se prenosi JSON izvozom/uvozom baze.


Od 3.4.0 u sadržaju su i sve beogradske gradske opštine; mogu se zasebno uređivati i isključivati. Raniji ceo Beograd (`m-beograd`) označen je kao istorijski zapis i ostaje radi prethodnih rezultata. U aktuelnoj opštinskoj igri je 190 teritorija. Ugrađene opštine dopunjuju i stariji JSON sadržaj, uz očuvane lokalne izmene; ako treba da se neka ne igra, isključi je umesto brisanja iz starije baze. Tekstovi ilustrovanih pravila i uvećanja zastava uređuju se kroz Prevode, a broj pokušaja u pravilima prati Podešavanja.

## Bodovanje i rang lista (3.5.0)

Nova javna stranica je `/srbija-na-dlanu/rang-lista/`, dostupna i iz menija. Prvi izbor je između Dnevnih izazova i Opšte rang liste. Zatim se biraju dan/mesec/sve vreme i sve igre/okruzi/opštine. Dnevna lista izazova računa prvi rezervisani završeni rezultat naloga po vrsti igre i datumu; opšta sabira i sva završena vežbanja. Jednaki zbirovi dele mesto. Veći broj odigranih vežbanja znači više mogućih poena na opštoj listi.

Podešavanja sadrže maksimum za teritoriju (60), umanjenje za dodatni pokušaj (6), pomoć (10) i uključivanje rang liste. Dodatna pitanja dele ostatak do 100; preskakanje i pomoć izvora donose 0 za to pitanje. Prikaz izvora posle odgovora ne umanjuje rezultat. Ako nema dodatnih pitanja, teritorija vredi 100. Politika se zamrzava u novoj partiji i dnevnom izazovu. Nema nagrade za brzinu ni naknadnog bodovanja starih partija. Gostujuća partija preuzeta na nalog čuva progres, ali ne ulazi naknadno u rang listu.

Podmeni Rang lista prikazuje i skrivene naloge, njihove javne nadimke, bodove, broj partija i prosek, uz filtere i CSV izvoz. Javna stranica ne otkriva ID, e-poštu ili korisničko ime. Podrazumevani nadimak je pseudonim; igrač na svom nalogu može upisati nadimak i sakriti sve rezultate. Tabela bodova se čuva nezavisno od analitike. WP privatni izvoz obuhvata rezultate i javni profil; brisanje privatnih podataka ili WP naloga uklanja rang rezultate. Dnevno čišćenje uklanja samo nezavršene rezervacije starije od 31 dana.

U Izgledu se podešavaju žuti akcent (podrazumevano #FFD447), boja pozadine dugmadi u tamnoj temi i dekorativna pozadina reka. Beli tekst ostaje na dugmadima tamne teme; izaberi dovoljno tamnu pozadinu zbog čitljivosti.
