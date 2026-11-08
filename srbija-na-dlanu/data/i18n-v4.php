<?php
defined('ABSPATH') || exit;
return json_decode(<<<'SND_V4_LANGUAGES'
{
  "Igre": {
    "en": "Games",
    "de": "Spiele",
    "ru": "Игры"
  },
  "Gradovi i naselja": {
    "en": "Cities and settlements",
    "de": "Städte und Ortschaften",
    "ru": "Города и населённые пункты"
  },
  "Reke": {
    "en": "Rivers",
    "de": "Flüsse",
    "ru": "Реки"
  },
  "Planine": {
    "en": "Mountains",
    "de": "Berge",
    "ru": "Горы"
  },
  "Pet geografskih izazova. Svaki dan novo otkriće.": {
    "en": "Five geography challenges. A new discovery every day.",
    "de": "Fünf Geografie-Rätsel. Jeden Tag eine neue Entdeckung.",
    "ru": "Пять географических задач. Новое открытие каждый день."
  },
  "{count} unosa u bazi": {
    "en": "{count} catalogue entries",
    "de": "{count} Katalogeinträge",
    "ru": "{count} объектов в базе"
  },
  "Današnji izazov": {
    "en": "Today's challenge",
    "de": "Heutige Herausforderung",
    "ru": "Задача на сегодня"
  },
  "Nastavi današnji izazov": {
    "en": "Continue today's challenge",
    "de": "Heutige Aufgabe fortsetzen",
    "ru": "Продолжить задачу дня"
  },
  "Završeno": {
    "en": "Completed",
    "de": "Abgeschlossen",
    "ru": "Завершено"
  },
  "Pogledaj rezultat": {
    "en": "View result",
    "de": "Ergebnis ansehen",
    "ru": "Посмотреть результат"
  },
  "Nastavi": {
    "en": "Continue",
    "de": "Fortsetzen",
    "ru": "Продолжить"
  },
  "Upoznaj Srbiju": {
    "en": "Explore Serbia",
    "de": "Serbien entdecken",
    "ru": "Откройте Сербию"
  },
  "Istraži okruge, opštine, naselja, reke i planine.": {
    "en": "Explore districts, municipalities, settlements, rivers and mountains.",
    "de": "Entdecke Bezirke, Gemeinden, Orte, Flüsse und Berge.",
    "ru": "Изучайте округа, общины, населённые пункты, реки и горы."
  },
  "Pogledaj mapu": {
    "en": "Look at the map",
    "de": "Karte ansehen",
    "ru": "Посмотрите на карту"
  },
  "Prepoznaj obris, pin naselja ili planine, ili označeni tok reke. Sever je gore.": {
    "en": "Identify an outline, a settlement or mountain pin, or a highlighted river. North is up.",
    "de": "Erkenne einen Umriss, einen Orts- oder Bergpin oder einen markierten Fluss. Norden ist oben.",
    "ru": "Узнайте контур, точку населённого пункта или горы либо выделенную реку. Север сверху."
  },
  "Pinovi, kilometri i strelice vode ka odgovoru. Kod reka uporedi označene tokove.": {
    "en": "Pins, distances and arrows guide you. For rivers, compare the highlighted courses.",
    "de": "Pins, Entfernungen und Pfeile helfen. Bei Flüssen vergleiche die markierten Verläufe.",
    "ru": "Точки, расстояния и стрелки помогают найти ответ. Для рек сравнивайте выделенные русла."
  },
  "Odgovori na četiri pitanja, prepoznaj fotografiju ili zastavu i osvoji do 100 poena.": {
    "en": "Answer four questions, recognise a photo or flag and earn up to 100 points.",
    "de": "Beantworte vier Fragen, erkenne ein Foto oder eine Flagge und sammle bis zu 100 Punkte.",
    "ru": "Ответьте на четыре вопроса, узнайте фото или флаг и получите до 100 очков."
  },
  "Jedan zadatak dnevno. Isti izazov za sve.": {
    "en": "One challenge a day. The same task for everyone.",
    "de": "Eine Aufgabe pro Tag. Für alle dieselbe Herausforderung.",
    "ru": "Одна задача в день. Одинаковая для всех."
  },
  "Koje je naselje označeno na mapi?": {
    "en": "Which settlement is marked on the map?",
    "de": "Welcher Ort ist auf der Karte markiert?",
    "ru": "Какой населённый пункт отмечен на карте?"
  },
  "Koja je reka označena na mapi?": {
    "en": "Which river is highlighted on the map?",
    "de": "Welcher Fluss ist auf der Karte markiert?",
    "ru": "Какая река выделена на карте?"
  },
  "Koja je planina označena na mapi?": {
    "en": "Which mountain is marked on the map?",
    "de": "Welcher Berg ist auf der Karte markiert?",
    "ru": "Какая гора отмечена на карте?"
  },
  "Naziv naselja": {
    "en": "Settlement name",
    "de": "Ortsname",
    "ru": "Название населённого пункта"
  },
  "Naziv reke": {
    "en": "River name",
    "de": "Flussname",
    "ru": "Название реки"
  },
  "Naziv planine": {
    "en": "Mountain name",
    "de": "Bergname",
    "ru": "Название горы"
  },
  "Unesi naselje…": {
    "en": "Enter a settlement…",
    "de": "Ort eingeben…",
    "ru": "Введите населённый пункт…"
  },
  "Unesi reku…": {
    "en": "Enter a river…",
    "de": "Fluss eingeben…",
    "ru": "Введите реку…"
  },
  "Unesi planinu…": {
    "en": "Enter a mountain…",
    "de": "Berg eingeben…",
    "ru": "Введите гору…"
  },
  "Označeno na mapi": {
    "en": "Marked on the map",
    "de": "Auf der Karte markiert",
    "ru": "Отмечено на карте"
  },
  "Označeni tokovi reka na mapi Srbije": {
    "en": "Highlighted rivers on the map of Serbia",
    "de": "Markierte Flüsse auf der Karte Serbiens",
    "ru": "Выделенные реки на карте Сербии"
  },
  "Označeni položaji na mapi Srbije": {
    "en": "Marked locations on the map of Serbia",
    "de": "Markierte Orte auf der Karte Serbiens",
    "ru": "Отмеченные точки на карте Сербии"
  },
  "Uvećaj mapu": {
    "en": "Enlarge map",
    "de": "Karte vergrößern",
    "ru": "Увеличить карту"
  },
  "Uvećana mapa": {
    "en": "Enlarged map",
    "de": "Vergrößerte Karte",
    "ru": "Увеличенная карта"
  },
  "Zadati odgovor": {
    "en": "Target answer",
    "de": "Gesuchte Antwort",
    "ru": "Заданный ответ"
  },
  "Prepoznaj istaknuti tok. Severno je gore.": {
    "en": "Identify the highlighted river. North is up.",
    "de": "Erkenne den markierten Fluss. Norden ist oben.",
    "ru": "Узнайте выделенную реку. Север сверху."
  },
  "Pin označava referentni vrh planine iz pregleda PSS.": {
    "en": "The pin marks the reference peak listed by the Mountaineering Association of Serbia.",
    "de": "Der Pin markiert den Referenzgipfel im Verzeichnis des serbischen Bergsteigerverbands.",
    "ru": "Точка обозначает вершину из перечня Альпинистского союза Сербии."
  },
  "Pin označava položaj naselja. Severno je gore.": {
    "en": "The pin marks the settlement. North is up.",
    "de": "Der Pin markiert den Ort. Norden ist oben.",
    "ru": "Точка обозначает населённый пункт. Север сверху."
  },
  "Današnji zadatak je završen. Novi izazov stiže u ponoć po vremenu u Srbiji.": {
    "en": "Today's task is complete. A new challenge arrives at midnight in Serbia.",
    "de": "Die heutige Aufgabe ist abgeschlossen. Um Mitternacht serbischer Zeit kommt eine neue.",
    "ru": "Задача дня завершена. Новая появится в полночь по времени Сербии."
  },
  "Izaberi drugu igru": {
    "en": "Choose another game",
    "de": "Anderes Spiel wählen",
    "ru": "Выбрать другую игру"
  },
  "Fotografije: autori i licence": {
    "en": "Photos: authors and licences",
    "de": "Fotos: Urheber und Lizenzen",
    "ru": "Фотографии: авторы и лицензии"
  },
  "Otvaranje izvora može otkriti odgovor i donosi 0 poena za ovo pitanje.": {
    "en": "Opening the sources may reveal the answer and gives 0 points for this question.",
    "de": "Quellen können die Antwort verraten; diese Frage bringt dann 0 Punkte.",
    "ru": "Источник может раскрыть ответ; за этот вопрос будет 0 очков."
  },
  "Prikaži izvore fotografija (pomoć)": {
    "en": "Show photo sources (hint)",
    "de": "Fotoquellen anzeigen (Hilfe)",
    "ru": "Показать источники фотографий (подсказка)"
  },
  "Koja fotografija prikazuje ovo naselje?": {
    "en": "Which photo shows this settlement?",
    "de": "Welches Foto zeigt diesen Ort?",
    "ru": "Какая фотография показывает этот населённый пункт?"
  },
  "Koja fotografija prikazuje ovu reku?": {
    "en": "Which photo shows this river?",
    "de": "Welches Foto zeigt diesen Fluss?",
    "ru": "Какая фотография показывает эту реку?"
  },
  "Koja fotografija prikazuje ovu planinu?": {
    "en": "Which photo shows this mountain?",
    "de": "Welches Foto zeigt diesen Berg?",
    "ru": "Какая фотография показывает эту гору?"
  },
  "Fotografija prikazuje: {name}.": {
    "en": "The photo shows: {name}.",
    "de": "Das Foto zeigt: {name}.",
    "ru": "На фотографии: {name}."
  },
  "Kojem okrugu pripada ovo naselje?": {
    "en": "Which district contains this settlement?",
    "de": "Zu welchem Bezirk gehört dieser Ort?",
    "ru": "В каком округе находится этот населённый пункт?"
  },
  "Naselje {name} pripada celini: {answer}.": {
    "en": "The settlement {name} belongs to: {answer}.",
    "de": "Der Ort {name} gehört zu: {answer}.",
    "ru": "Населённый пункт {name} относится к округу: {answer}."
  },
  "Koliko približno stanovnika ima naselje?": {
    "en": "Approximately how many people live in the settlement?",
    "de": "Wie viele Einwohner hat ungefähr der Ort?",
    "ru": "Сколько примерно жителей в населённом пункте?"
  },
  "Podatak u izvoru: {value} {unit}. {detail}": {
    "en": "Source value: {value} {unit}. {detail}",
    "de": "Angabe der Quelle: {value} {unit}. {detail}",
    "ru": "Данные источника: {value} {unit}. {detail}"
  },
  "Popis 2022 · stanovništvo naselja, ne cele opštine.": {
    "en": "2022 census · settlement population, not the entire municipality.",
    "de": "Volkszählung 2022 · Einwohner des Ortes, nicht der gesamten Gemeinde.",
    "ru": "Перепись 2022 · население пункта, а не всей общины."
  },
  "U kom širem delu Srbije se nalazi ovo naselje?": {
    "en": "In which broader part of Serbia is this settlement?",
    "de": "In welcher größeren Region Serbiens liegt dieser Ort?",
    "ru": "В какой части Сербии находится этот населённый пункт?"
  },
  "Širi položaj: {answer}.": {
    "en": "Broader location: {answer}.",
    "de": "Größere Region: {answer}.",
    "ru": "Географический район: {answer}."
  },
  "Koja reka prolazi kroz ovo naselje ili pored njega?": {
    "en": "Which river flows through or beside this settlement?",
    "de": "Welcher Fluss fließt durch oder an diesem Ort vorbei?",
    "ru": "Какая река протекает через этот населённый пункт или рядом с ним?"
  },
  "Uz naselje {name} je reka {answer}.": {
    "en": "The river by {name} is {answer}.",
    "de": "Bei {name} fließt der Fluss {answer}.",
    "ru": "Рядом с {name} протекает река {answer}."
  },
  "U kom smeru je mesto „{anchor}“ u odnosu na označeni položaj?": {
    "en": "In which direction is “{anchor}” from the marked point?",
    "de": "In welcher Richtung liegt „{anchor}“ vom markierten Punkt aus?",
    "ru": "В каком направлении находится «{anchor}» от отмеченной точки?"
  },
  "Od označenog položaja ka mestu {anchor}: {answer}.": {
    "en": "From the marked point towards {anchor}: {answer}.",
    "de": "Vom markierten Punkt nach {anchor}: {answer}.",
    "ru": "От отмеченной точки к {anchor}: {answer}."
  },
  "Gde se neposredno uliva ova reka?": {
    "en": "Where does this river flow into directly?",
    "de": "In welches Gewässer mündet dieser Fluss direkt?",
    "ru": "Куда непосредственно впадает эта река?"
  },
  "Koja reka nastaje na sastavu ove reke?": {
    "en": "Which river is formed at this river's confluence?",
    "de": "Welcher Fluss entsteht am Zusammenfluss dieses Flusses?",
    "ru": "Какая река образуется при слиянии этой реки?"
  },
  "Reka {name}: neposredni recipijent je {answer}.": {
    "en": "River {name}: its immediate receiving water is {answer}.",
    "de": "Fluss {name}: Das unmittelbar aufnehmende Gewässer ist {answer}.",
    "ru": "Река {name}: непосредственно впадает в {answer}."
  },
  "Na sastavu reke {name} nastaje {answer}.": {
    "en": "River {answer} is formed at the confluence of {name}.",
    "de": "Am Zusammenfluss von {name} entsteht {answer}.",
    "ru": "При слиянии реки {name} образуется {answer}."
  },
  "Kojem morskom slivu pripada ova reka?": {
    "en": "Which sea basin does this river belong to?",
    "de": "Zu welchem Meeresbecken gehört dieser Fluss?",
    "ru": "К бассейну какого моря относится эта река?"
  },
  "Sliv reke {name} završava u moru: {answer}.": {
    "en": "The drainage basin of {name} ends in: {answer}.",
    "de": "Das Einzugsgebiet von {name} entwässert in: {answer}.",
    "ru": "Бассейн реки {name} относится к морю: {answer}."
  },
  "Kolika je približna ukupna dužina reke?": {
    "en": "What is the river's approximate total length?",
    "de": "Wie lang ist der Fluss insgesamt ungefähr?",
    "ru": "Какова приблизительная общая длина реки?"
  },
  "Cela reka, uključujući deo van Srbije.": {
    "en": "The whole river, including its course outside Serbia.",
    "de": "Der gesamte Fluss, einschließlich des Verlaufs außerhalb Serbiens.",
    "ru": "Вся река, включая течение за пределами Сербии."
  },
  "Crno more": {
    "en": "Black Sea",
    "de": "Schwarzes Meer",
    "ru": "Чёрное море"
  },
  "Jadransko more": {
    "en": "Adriatic Sea",
    "de": "Adriatisches Meer",
    "ru": "Адриатическое море"
  },
  "Egejsko more": {
    "en": "Aegean Sea",
    "de": "Ägäisches Meer",
    "ru": "Эгейское море"
  },
  "Baltičko more": {
    "en": "Baltic Sea",
    "de": "Ostsee",
    "ru": "Балтийское море"
  },
  "Koji je najviši vrh prema navedenom obuhvatu planine?": {
    "en": "What is the highest peak within the stated mountain extent?",
    "de": "Welcher Gipfel ist im angegebenen Gebirgsumfang der höchste?",
    "ru": "Какая вершина самая высокая в указанном горном районе?"
  },
  "Planina {name}: vrh {answer}. {scope}": {
    "en": "Mountain {name}: peak {answer}. {scope}",
    "de": "Berg {name}: Gipfel {answer}. {scope}",
    "ru": "Гора {name}: вершина {answer}. {scope}"
  },
  "Kolika je približna visina tog vrha?": {
    "en": "What is the approximate elevation of that peak?",
    "de": "Wie hoch ist dieser Gipfel ungefähr?",
    "ru": "Какова приблизительная высота этой вершины?"
  },
  "Kolika je približna površina navedenog planinskog obuhvata?": {
    "en": "What is the approximate area of the stated mountain extent?",
    "de": "Wie groß ist die Fläche des angegebenen Gebirgsumfangs ungefähr?",
    "ru": "Какова приблизительная площадь указанного горного района?"
  },
  "U kom širem delu Srbije je označena planina?": {
    "en": "In which broader part of Serbia is the marked mountain?",
    "de": "In welcher größeren Region Serbiens liegt der markierte Berg?",
    "ru": "В какой части Сербии находится отмеченная гора?"
  },
  "Označeni položaj planine {name}: {answer}.": {
    "en": "Marked location of {name}: {answer}.",
    "de": "Markierter Standort von {name}: {answer}.",
    "ru": "Отмеченное местоположение горы {name}: {answer}."
  },
  "Najviši vrh prema pregledu PSS za Srbiju": {
    "en": "Highest peak according to the PSS list for Serbia",
    "de": "Höchster Gipfel laut PSS-Verzeichnis für Serbien",
    "ru": "Самая высокая вершина по перечню PSS для Сербии"
  },
  "Najviši vrh u Srbiji prema pregledu PSS.": {
    "en": "Highest peak in Serbia according to the PSS list.",
    "de": "Höchster Gipfel in Serbien laut PSS-Verzeichnis.",
    "ru": "Самая высокая вершина в Сербии по перечню PSS."
  },
  "Vrh Đeravica u pregledu PSS za Srbiju; ne najviši vrh celog prekograničnog masiva.": {
    "en": "Đeravica in the PSS list for Serbia; the entire cross-border range has a different highest peak.",
    "de": "Đeravica im PSS-Verzeichnis für Serbien; der gesamte grenzüberschreitende Gebirgszug hat einen anderen höchsten Gipfel.",
    "ru": "Джеравица в перечне PSS для Сербии; самая высокая вершина всего трансграничного массива другая."
  },
  "Vrh Velika Rudoka u pregledu PSS za Srbiju; ne najviši vrh celog prekograničnog masiva.": {
    "en": "Velika Rudoka in the PSS list for Serbia; the entire cross-border range has a different highest peak.",
    "de": "Velika Rudoka im PSS-Verzeichnis für Serbien; der gesamte grenzüberschreitende Gebirgszug hat einen anderen höchsten Gipfel.",
    "ru": "Велика-Рудока в перечне PSS для Сербии; самая высокая вершина всего трансграничного массива другая."
  },
  "Planina Beljanica, prema opisu geografskog obuhvata u navedenom izvoru.": {
    "en": "Beljanica mountain, using the geographical extent described in the cited source.",
    "de": "Berg Beljanica gemäß dem geografischen Umfang der angegebenen Quelle.",
    "ru": "Гора Бельяница в географических пределах, описанных в указанном источнике."
  },
  "Jedan rezultat po igri i danu. Ukupno do 500 poena dnevno za pet igara. Vežbanje ne ulazi u plasman.": {
    "en": "One result per game per day. Up to 500 daily points across five games. Practice does not count.",
    "de": "Ein Ergebnis je Spiel und Tag. Bis zu 500 Punkte täglich in fünf Spielen. Übung zählt nicht.",
    "ru": "Один результат на игру в день. До 500 очков за пять игр. Тренировки не учитываются."
  },
  "Nova sezona dnevnih igara. Raniji rezultati ostaju u istoriji naloga.": {
    "en": "A new season of daily games. Previous results remain in account history.",
    "de": "Neue Saison der täglichen Spiele. Frühere Ergebnisse bleiben im Kontoverlauf.",
    "ru": "Новый сезон ежедневных игр. Старые результаты остаются в истории аккаунта."
  },
  "Rang lista dnevnih izazova za svih pet igara.": {
    "en": "Daily challenge leaderboard for all five games.",
    "de": "Rangliste der täglichen Herausforderungen aller fünf Spiele.",
    "ru": "Рейтинг ежедневных задач всех пяти игр."
  },
  "Dnevne geografske igre: gradovi i naselja, reke i planine. Mapa, šest pokušaja, fotografije i rang lista.": {
    "en": "Daily geography games: settlements, rivers and mountains. Maps, six guesses, photos and a leaderboard.",
    "de": "Tägliche Geografie-Spiele: Orte, Flüsse und Berge. Karte, sechs Versuche, Fotos und Rangliste.",
    "ru": "Ежедневные географические игры: населённые пункты, реки и горы. Карта, шесть попыток, фото и рейтинг."
  },
  "Uporedi označene tokove reka. Kilometri između reka se ne prikazuju.": {
    "en": "Compare the highlighted river courses. Distances between rivers are not shown.",
    "de": "Vergleiche die markierten Flussläufe. Abstände zwischen Flüssen werden nicht angezeigt.",
    "ru": "Сравните выделенные русла. Расстояния между реками не показываются."
  },
  "Pinovi označavaju proverene referentne tačke. Kilometri i strelica vode od tvog odgovora ka zadatom pinu.": {
    "en": "Pins mark verified reference points. Distance and arrow lead from your guess to the target pin.",
    "de": "Pins markieren geprüfte Referenzpunkte. Entfernung und Pfeil führen von deiner Antwort zum gesuchten Pin.",
    "ru": "Точки обозначают проверенные положения. Расстояние и стрелка направлены от ответа к заданной точке."
  },
  "Igra se jednom dnevno. Otvori današnji izazov.": {
    "en": "Play once a day. Open today's challenge.",
    "de": "Ein Spiel pro Tag. Öffne die heutige Aufgabe.",
    "ru": "Игра доступна раз в день. Откройте сегодняшнюю задачу."
  },
  "Izaberi na mapi ili pronađi naziv u tabeli.": {
    "en": "Choose on the map or find a name in the table.",
    "de": "Wähle auf der Karte oder suche einen Namen in der Tabelle.",
    "ru": "Выберите на карте или найдите название в таблице."
  },
  "Vrsta geografskih podataka": {
    "en": "Geographical category",
    "de": "Geografische Kategorie",
    "ru": "Категория географических данных"
  },
  "Mapa geografskih objekata": {
    "en": "Map of geographical features",
    "de": "Karte geografischer Objekte",
    "ru": "Карта географических объектов"
  },
  "Pronađi naziv": {
    "en": "Find a name",
    "de": "Namen suchen",
    "ru": "Найти название"
  },
  "Pronađi naziv…": {
    "en": "Find a name…",
    "de": "Namen suchen…",
    "ru": "Найти название…"
  },
  "Naziv": {
    "en": "Name",
    "de": "Name",
    "ru": "Название"
  },
  "Okrug": {
    "en": "District",
    "de": "Bezirk",
    "ru": "Округ"
  },
  "Položaj": {
    "en": "Location",
    "de": "Lage",
    "ru": "Местоположение"
  },
  "Uliva se u": {
    "en": "Flows into",
    "de": "Mündet in",
    "ru": "Впадает в"
  },
  "Najviši vrh": {
    "en": "Highest peak",
    "de": "Höchster Gipfel",
    "ru": "Самая высокая вершина"
  },
  "Visina": {
    "en": "Elevation",
    "de": "Höhe",
    "ru": "Высота"
  },
  "Obuhvat": {
    "en": "Extent",
    "de": "Umfang",
    "ru": "Область измерения"
  },
  "Površina": {
    "en": "Area",
    "de": "Fläche",
    "ru": "Площадь"
  },
  "Stanovništvo naselja": {
    "en": "Settlement population",
    "de": "Einwohner des Ortes",
    "ru": "Население пункта"
  },
  "Stanovništvo naselja · 2022": {
    "en": "Settlement population · 2022",
    "de": "Ortseinwohner · 2022",
    "ru": "Население пункта · 2022"
  },
  "Dužina · ceo tok": {
    "en": "Length · entire river",
    "de": "Länge · gesamter Fluss",
    "ru": "Длина · вся река"
  },
  "Morski sliv": {
    "en": "Sea basin",
    "de": "Meeresbecken",
    "ru": "Морской бассейн"
  },
  "Izvor fotografije": {
    "en": "Photo source",
    "de": "Fotoquelle",
    "ru": "Источник фотографии"
  },
  "Izvor podataka": {
    "en": "Data source",
    "de": "Datenquelle",
    "ru": "Источник данных"
  },
  "Geografski izvor": {
    "en": "Geographical source",
    "de": "Geografische Quelle",
    "ru": "Географический источник"
  },
  "Igraj današnji izazov": {
    "en": "Play today's challenge",
    "de": "Heutige Aufgabe spielen",
    "ru": "Играть задачу дня"
  },
  "Nedostajući podaci su označeni crticom. Dnevni fond koristi kompletne, proverene stavke sa fotografijama. Katalog uključuje Kosovo i Metohiju.": {
    "en": "Missing data is shown with a dash. Daily challenges use complete, verified entries with photos. The catalogue includes Kosovo and Metohija.",
    "de": "Fehlende Daten sind mit einem Strich markiert. Tägliche Aufgaben nutzen vollständige, geprüfte Einträge mit Fotos. Der Katalog umfasst Kosovo und Metochien.",
    "ru": "Отсутствующие данные обозначены прочерком. Ежедневные задачи используют проверенные записи с фото. Каталог включает Косово и Метохию."
  },
  "Pin označava referentni vrh iz pregleda PSS; precizan obris masiva nije izmišljen.": {
    "en": "The pin marks the reference peak in the PSS list; no precise massif boundary is implied.",
    "de": "Der Pin markiert den Referenzgipfel im PSS-Verzeichnis; eine genaue Massivgrenze wird nicht behauptet.",
    "ru": "Точка обозначает вершину из перечня PSS; точные границы массива не подразумеваются."
  },
  "Na mapi je deo toka na teritoriji Srbije; podatak o dužini odnosi se na celu reku.": {
    "en": "The map shows the course in Serbia; the length refers to the entire river.",
    "de": "Die Karte zeigt den Verlauf in Serbien; die Länge bezieht sich auf den gesamten Fluss.",
    "ru": "На карте показано течение в Сербии; длина относится ко всей реке."
  },
  "Stanovništvo se odnosi na naselje, ne na celu opštinu.": {
    "en": "Population refers to the settlement, not the entire municipality.",
    "de": "Die Einwohnerzahl bezieht sich auf den Ort, nicht die gesamte Gemeinde.",
    "ru": "Население относится к населённому пункту, а не всей общине."
  },
  "Fotografija A": {
    "en": "Photo A",
    "de": "Foto A",
    "ru": "Фото A"
  },
  "Fotografija B": {
    "en": "Photo B",
    "de": "Foto B",
    "ru": "Фото B"
  },
  "Fotografija C": {
    "en": "Photo C",
    "de": "Foto C",
    "ru": "Фото C"
  },
  "Fotografija D": {
    "en": "Photo D",
    "de": "Foto D",
    "ru": "Фото D"
  },
  "Sever": {
    "en": "North",
    "de": "Norden",
    "ru": "Север"
  },
  "Severoistok": {
    "en": "Northeast",
    "de": "Nordosten",
    "ru": "Северо-восток"
  },
  "Istok": {
    "en": "East",
    "de": "Osten",
    "ru": "Восток"
  },
  "Jugoistok": {
    "en": "Southeast",
    "de": "Südosten",
    "ru": "Юго-восток"
  },
  "Jug": {
    "en": "South",
    "de": "Süden",
    "ru": "Юг"
  },
  "Jugozapad": {
    "en": "Southwest",
    "de": "Südwesten",
    "ru": "Юго-запад"
  },
  "Zapad": {
    "en": "West",
    "de": "Westen",
    "ru": "Запад"
  },
  "Severozapad": {
    "en": "Northwest",
    "de": "Nordwesten",
    "ru": "Северо-запад"
  },
  "Beograd": {
    "en": "Belgrade",
    "de": "Belgrad",
    "ru": "Белград"
  },
  "Vojvodina": {
    "en": "Vojvodina",
    "de": "Vojvodina",
    "ru": "Воеводина"
  },
  "Zapadna Srbija": {
    "en": "Western Serbia",
    "de": "Westserbien",
    "ru": "Западная Сербия"
  },
  "Šumadija i Pomoravlje": {
    "en": "Šumadija and Pomoravlje",
    "de": "Šumadija und Pomoravlje",
    "ru": "Шумадия и Поморавье"
  },
  "Istočna Srbija": {
    "en": "Eastern Serbia",
    "de": "Ostserbien",
    "ru": "Восточная Сербия"
  },
  "Južna Srbija": {
    "en": "Southern Serbia",
    "de": "Südserbien",
    "ru": "Южная Сербия"
  },
  "Kosovo i Metohija": {
    "en": "Kosovo and Metohija",
    "de": "Kosovo und Metohija",
    "ru": "Косово и Метохия"
  },
  "Igre — Srbija na dlanu": {
    "en": "Games — Srbija na dlanu",
    "de": "Spiele — Srbija na dlanu",
    "ru": "Игры — Srbija na dlanu"
  },
  "Gradovi i naselja — Srbija na dlanu": {
    "en": "Cities and settlements — Srbija na dlanu",
    "de": "Städte und Ortschaften — Srbija na dlanu",
    "ru": "Города и населённые пункты — Srbija na dlanu"
  },
  "Reke — Srbija na dlanu": {
    "en": "Rivers — Srbija na dlanu",
    "de": "Flüsse — Srbija na dlanu",
    "ru": "Реки — Srbija na dlanu"
  },
  "Planine — Srbija na dlanu": {
    "en": "Mountains — Srbija na dlanu",
    "de": "Berge — Srbija na dlanu",
    "ru": "Горы — Srbija na dlanu"
  }
}
SND_V4_LANGUAGES
,true);
