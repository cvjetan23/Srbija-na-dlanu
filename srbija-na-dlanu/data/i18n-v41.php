<?php
defined('ABSPATH') || exit;
return json_decode(<<<'SND_V41_LANGUAGES'
{
  "Sve igre": {
    "en": "All games",
    "de": "Alle Spiele",
    "ru": "Все игры"
  },
  "Prepoznaj okrug po obrisu.": {
    "en": "Recognise a district by its outline.",
    "de": "Erkenne einen Bezirk an seinem Umriss.",
    "ru": "Узнай округ по его контуру."
  },
  "Prepoznaj opštinu ili grad po obrisu.": {
    "en": "Recognise a municipality or city by its outline.",
    "de": "Erkenne eine Gemeinde oder Stadt an ihrem Umriss.",
    "ru": "Узнай общину или город по контуру."
  },
  "Pronađi naselje označeno pinom.": {
    "en": "Find the settlement marked with a pin.",
    "de": "Finde den mit einer Markierung gezeigten Ort.",
    "ru": "Найди отмеченный населённый пункт."
  },
  "Prepoznaj istaknuti tok reke.": {
    "en": "Recognise the highlighted river.",
    "de": "Erkenne den hervorgehobenen Flusslauf.",
    "ru": "Узнай выделенную реку."
  },
  "Pronađi planinu označenu na mapi.": {
    "en": "Find the mountain marked on the map.",
    "de": "Finde das auf der Karte markierte Gebirge.",
    "ru": "Найди гору, отмеченную на карте."
  },
  "Upoznaj Srbiju kroz igru": {
    "en": "Discover Serbia through play",
    "de": "Entdecke Serbien spielerisch",
    "ru": "Узнавай Сербию в игре"
  },
  "Od obrisa okruga i opština do gradova, reka i planina — svakog dana pronađi nešto novo na mapi. Pokušaji daju tragove, a dodatna pitanja i fotografije pomažu da zapamtiš ono što si otkrio.": {
    "en": "From district and municipality outlines to towns, rivers and mountains, discover something new on the map each day. Each guess gives a clue, while extra questions and photographs help you remember what you discover.",
    "de": "Von Bezirks- und Gemeindeumrissen bis zu Orten, Flüssen und Bergen: Entdecke jeden Tag etwas Neues auf der Karte. Jeder Versuch liefert Hinweise; Zusatzfragen und Fotos helfen dir, das Entdeckte zu behalten.",
    "ru": "От контуров округов и общин до городов, рек и гор — каждый день открывай что-то новое на карте. Попытки дают подсказки, а дополнительные вопросы и фотографии помогают запомнить открытия."
  },
  "Igraj po jedan izazov iz svake igre dnevno, sačuvaj progres na nalogu i uporedi rezultat na rang listi. Kada poželiš da istražiš više, otvori bazu znanja.": {
    "en": "Play one challenge per game each day, save your progress to your account and compare your score on the leaderboard. Visit the knowledge base whenever you want to explore further.",
    "de": "Spiele täglich eine Aufgabe je Spiel, speichere deinen Fortschritt im Konto und vergleiche dein Ergebnis in der Rangliste. In der Wissensdatenbank kannst du anschließend weiterforschen.",
    "ru": "Проходи по одному заданию каждой игры в день, сохраняй прогресс в аккаунте и сравнивай результаты в рейтинге. Для дальнейшего изучения открывай базу знаний."
  },
  "Gradska opština {name} pripada gradu {city}.": {
    "en": "The city municipality {name} belongs to the city of {city}.",
    "de": "Die Stadtgemeinde {name} gehört zur Stadt {city}.",
    "ru": "Городская община {name} относится к городу {city}."
  },
  "Spisak opština je ažuriran. Započeta partija zadržava ranije granice.": {
    "en": "The municipality list has been updated. Your current game keeps its original boundaries.",
    "de": "Die Gemeindeliste wurde aktualisiert. Dein laufendes Spiel behält seine bisherigen Grenzen.",
    "ru": "Список общин обновлён. Начатая партия сохраняет прежние границы."
  },
  "Izaberi naziv iz proverenog spiska.": {
    "en": "Choose a name from the verified list.",
    "de": "Wähle einen Namen aus der geprüften Liste.",
    "ru": "Выбери название из проверенного списка."
  },
  "Filteri": {
    "en": "Filters",
    "de": "Filter",
    "ru": "Фильтры"
  },
  "Filteri rang liste": {
    "en": "Leaderboard filters",
    "de": "Filter der Rangliste",
    "ru": "Фильтры рейтинга"
  },
  "Period": {
    "en": "Period",
    "de": "Zeitraum",
    "ru": "Период"
  },
  "Pronađi naziv": {
    "en": "Find a name",
    "de": "Name suchen",
    "ru": "Найти название"
  },
  "Pronađi naziv…": {
    "en": "Find a name…",
    "de": "Name suchen…",
    "ru": "Найти название…"
  },
  "Svi slivovi": {
    "en": "All sea basins",
    "de": "Alle Meeresgebiete",
    "ru": "Все морские бассейны"
  },
  "Sve oblasti": {
    "en": "All regions",
    "de": "Alle Regionen",
    "ru": "Все регионы"
  },
  "Gradske opštine": {
    "en": "City municipalities",
    "de": "Stadtgemeinden",
    "ru": "Городские общины"
  },
  "Sve opštine i gradovi": {
    "en": "All municipalities and cities",
    "de": "Alle Gemeinden und Städte",
    "ru": "Все общины и города"
  },
  "Poređaj po": {
    "en": "Sort by",
    "de": "Sortieren nach",
    "ru": "Сортировка"
  },
  "Naziv A–Ž": {
    "en": "Name A–Z",
    "de": "Name A–Z",
    "ru": "Название А–Я"
  },
  "Dužina — najduže prvo": {
    "en": "Length — longest first",
    "de": "Länge — längste zuerst",
    "ru": "Длина — сначала самые длинные"
  },
  "Visina — najviše prvo": {
    "en": "Elevation — highest first",
    "de": "Höhe — höchste zuerst",
    "ru": "Высота — сначала самые высокие"
  },
  "Stanovništvo — najveće prvo": {
    "en": "Population — largest first",
    "de": "Bevölkerung — größte zuerst",
    "ru": "Население — сначала наибольшее"
  },
  "Poništi filtere": {
    "en": "Clear filters",
    "de": "Filter zurücksetzen",
    "ru": "Сбросить фильтры"
  },
  "← Nazad na spisak": {
    "en": "← Back to the list",
    "de": "← Zurück zur Liste",
    "ru": "← Назад к списку"
  },
  "Referentni vrh": {
    "en": "Reference peak",
    "de": "Referenzgipfel",
    "ru": "Опорная вершина"
  },
  "Stanovništvo · 2022": {
    "en": "Population · 2022",
    "de": "Bevölkerung · 2022",
    "ru": "Население · 2022"
  },
  "Opštine, gradske opštine i gradovi": {
    "en": "Municipalities, city municipalities and cities",
    "de": "Gemeinden, Stadtgemeinden und Städte",
    "ru": "Общины, городские общины и города"
  },
  "Spisak naselja": {
    "en": "Settlement list",
    "de": "Ortsverzeichnis",
    "ru": "Список населённых пунктов"
  },
  "Spisak reka": {
    "en": "River list",
    "de": "Flussverzeichnis",
    "ru": "Список рек"
  },
  "Spisak planina": {
    "en": "Mountain list",
    "de": "Gebirgsverzeichnis",
    "ru": "Список гор"
  },
  "Nema rezultata za izabrane filtere.": {
    "en": "No results match these filters.",
    "de": "Keine Ergebnisse für diese Filter.",
    "ru": "По выбранным фильтрам ничего не найдено."
  },
  "Kako se računa broj opština?": {
    "en": "How are municipalities counted?",
    "de": "Wie werden die Gemeinden gezählt?",
    "ru": "Как подсчитываются общины?"
  },
  "Spisak prati 197 opština, gradskih opština i gradova iz registra RZS. To nisu 197 samostalnih opština: Srbija ima 174 jedinice lokalne samouprave. Beograd, Niš, Požarevac, Vranje i Užice ovde su prikazani kroz 28 gradskih opština, bez ponovnog dodavanja celih gradova.": {
    "en": "This list follows the 197 municipalities, city municipalities and cities in the Serbian Statistical Office register. These are not 197 independent municipalities: Serbia has 174 local self-government units. Belgrade, Niš, Požarevac, Vranje and Užice are represented here by their 28 city municipalities, without counting the whole cities again.",
    "de": "Die Liste folgt den 197 Gemeinden, Stadtgemeinden und Städten im Register des serbischen Statistikamts. Das sind keine 197 selbstständigen Gemeinden: Serbien hat 174 kommunale Selbstverwaltungseinheiten. Belgrad, Niš, Požarevac, Vranje und Užice werden hier durch ihre 28 Stadtgemeinden dargestellt, ohne die gesamten Städte zusätzlich zu zählen.",
    "ru": "Список соответствует 197 общинам, городским общинам и городам в реестре статистического управления Сербии. Это не 197 самостоятельных общин: в Сербии 174 единицы местного самоуправления. Белград, Ниш, Пожаревац, Вране и Ужице представлены своими 28 городскими общинами, без повторного учёта целых городов."
  },
  "Uključeni su Kosovo i Metohija. Crtica znači da uporediv podatak iz Popisa 2022. nije dostupan.": {
    "en": "Kosovo and Metohija are included. A dash means comparable 2022 census data are unavailable.",
    "de": "Kosovo und Metochien sind enthalten. Ein Strich bedeutet, dass keine vergleichbaren Zensusdaten von 2022 vorliegen.",
    "ru": "Косово и Метохия включены. Прочерк означает отсутствие сопоставимых данных переписи 2022 года."
  },
  "Zvanična podela RZS ↗": {
    "en": "Official Statistical Office classification ↗",
    "de": "Offizielle Gliederung des Statistikamts ↗",
    "ru": "Официальное деление по данным статистического управления ↗"
  },
  "U javnom katalogu su potvrđeni nazivi. Nedostajući podaci označeni su crticom; dnevni zadaci imaju kompletna pitanja i fotografije.": {
    "en": "The public catalogue contains verified names. Missing facts are shown with a dash; daily challenges have complete questions and photographs.",
    "de": "Der öffentliche Katalog enthält bestätigte Namen. Fehlende Angaben sind mit einem Strich markiert; Tagesaufgaben haben vollständige Fragen und Fotos.",
    "ru": "В публичном каталоге приведены подтверждённые названия. Недостающие сведения отмечены прочерком; ежедневные задания содержат полный набор вопросов и фотографий."
  },
  "Potvrda naziva:": {
    "en": "Name verified by:",
    "de": "Namensbestätigung:",
    "ru": "Подтверждение названия:"
  },
  "Pregled Planinarskog saveza Srbije": {
    "en": "Serbian Mountaineering Association directory",
    "de": "Verzeichnis des serbischen Bergsteigerverbands",
    "ru": "Справочник Союза альпинистов Сербии"
  },
  "Pregled izvora za ovu reku": {
    "en": "Reference for this river",
    "de": "Quelle zu diesem Fluss",
    "ru": "Источник сведений об этой реке"
  },
  "Istraži prirodu Srbije": {
    "en": "Explore Serbia’s nature",
    "de": "Entdecke Serbiens Natur",
    "ru": "Изучай природу Сербии"
  },
  "Reke Srbije": {
    "en": "Rivers of Serbia",
    "de": "Flüsse Serbiens",
    "ru": "Реки Сербии"
  },
  "Planine Srbije": {
    "en": "Mountains of Serbia",
    "de": "Gebirge Serbiens",
    "ru": "Горы Сербии"
  },
  "Istraži tok, ušće i sliv reke.": {
    "en": "Explore a river’s course, mouth and basin.",
    "de": "Erkunde Verlauf, Mündung und Einzugsgebiet eines Flusses.",
    "ru": "Изучи течение, устье и бассейн реки."
  },
  "Pronađi planinu, njen vrh i visinu.": {
    "en": "Find a mountain, its peak and elevation.",
    "de": "Finde ein Gebirge, seinen Gipfel und seine Höhe.",
    "ru": "Найди гору, её вершину и высоту."
  },
  "Sve reke": {
    "en": "All rivers",
    "de": "Alle Flüsse",
    "ru": "Все реки"
  },
  "Sve planine": {
    "en": "All mountains",
    "de": "Alle Gebirge",
    "ru": "Все горы"
  },
  "Pregled izvora za ovo naselje": {
    "en": "Reference for this settlement",
    "de": "Quelle zu diesem Ort",
    "ru": "Источник об этом населённом пункте"
  },
  "Igra obuhvata upravne okruge, Grad Beograd i opštine, uključujući 17 gradskih opština Beograda i Kosovo i Metohiju. Za Kosovo i Metohiju koristi se podela na pet okruga i 29 opština. Stanovništvo je iz Popisa RZS 2022; gde uporediv podatak nije dostupan, postavlja se geografsko pitanje. Obrisi služe igri i nisu katastarske karte.": {
    "en": "The game covers 29 administrative districts and the City of Belgrade, plus 197 municipalities, city municipalities and cities according to the Statistical Office of Serbia. Five cities are represented by all 28 city municipalities, without overlapping whole-city records. Kosovo and Metohija are included with five districts and 29 municipalities. The Serbian 2022 census was not conducted there. Outlines are simplified for play and are not cadastral maps.",
    "de": "Das Spiel umfasst 29 Verwaltungsbezirke und die Stadt Belgrad sowie 197 Gemeinden, Stadtgemeinden und Städte gemäß dem serbischen Statistikamt. Fünf Städte werden durch alle 28 Stadtgemeinden dargestellt, ohne doppelte Gesamtstadtgebiete. Kosovo und Metochien sind mit fünf Bezirken und 29 Gemeinden enthalten. Die serbische Volkszählung 2022 fand dort nicht statt. Die Umrisse dienen dem Spiel und sind keine Katasterkarten.",
    "ru": "Игра охватывает 29 административных округов и город Белград, а также 197 общин, городских общин и городов по данным статистического ведомства Сербии. Пять городов представлены всеми 28 городскими общинами, без дублирования всей территории города. Включены Косово и Метохия: пять округов и 29 общин. Перепись Сербии 2022 года там не проводилась. Контуры упрощены для игры и не являются кадастровыми картами."
  }
}
SND_V41_LANGUAGES
,true);
