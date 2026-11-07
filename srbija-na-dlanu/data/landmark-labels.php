<?php
defined('ABSPATH') || exit;
// Neutral quiz labels. Official names remain in the atlas and answer explanations.
return json_decode(<<<'SND_LANDMARK_LABELS'
{
  "Beogradska tvrđava": {
    "sr": "Tvrđava uz Kalemegdan",
    "en": "Fortress by Kalemegdan",
    "de": "Festung am Kalemegdan",
    "ru": "Крепость у Калемегдана"
  },
  "Gradska kuća u Subotici": {
    "sr": "Gradska kuća u stilu secesije",
    "en": "Art Nouveau City Hall",
    "de": "Jugendstil-Rathaus",
    "ru": "Ратуша в стиле модерн"
  },
  "Dvorac Kaštel u Ečki": {
    "sr": "Dvorac Kaštel",
    "en": "Kaštel Castle",
    "de": "Schloss Kaštel",
    "ru": "Замок Каштел"
  },
  "Suvača u Kikindi": {
    "sr": "Suvača",
    "en": "Suvača Mill",
    "de": "Suvača-Mühle",
    "ru": "Мельница Сувача"
  },
  "Vršačka kula": {
    "sr": "Srednjovekovna kula na bregu",
    "en": "Medieval hilltop tower",
    "de": "Mittelalterlicher Turm auf dem Hügel",
    "ru": "Средневековая башня на холме"
  },
  "Gradska kuća u Somboru": {
    "sr": "Stara gradska kuća",
    "en": "Old City Hall",
    "de": "Altes Rathaus",
    "ru": "Старая ратуша"
  },
  "Petrovaradinska tvrđava": {
    "sr": "Tvrđava sa satom",
    "en": "Fortress with the clock tower",
    "de": "Festung mit dem Uhrturm",
    "ru": "Крепость с часовой башней"
  },
  "Carska palata Sirmijuma": {
    "sr": "Carska palata",
    "en": "Imperial Palace",
    "de": "Kaiserpalast",
    "ru": "Императорский дворец"
  },
  "Vukova kuća u Tršiću": {
    "sr": "Vukova rodna kuća",
    "en": "Vuk’s birthplace",
    "de": "Vuks Geburtshaus",
    "ru": "Родной дом Вука"
  },
  "Tešnjar": {
    "sr": "Tešnjar",
    "en": "Tešnjar",
    "de": "Tešnjar",
    "ru": "Тешняр"
  },
  "Smederevska tvrđava": {
    "sr": "Tvrđava despota Đurđa",
    "en": "Despot Đurađ’s fortress",
    "de": "Festung des Despoten Đurađ",
    "ru": "Крепость деспота Джураджа"
  },
  "Golubačka tvrđava": {
    "sr": "Tvrđava na ulazu u Đerdap",
    "en": "Fortress at the entrance to Đerdap",
    "de": "Festung am Eingang zum Đerdap",
    "ru": "Крепость у входа в Джердап"
  },
  "Crkva Svetog Đorđa na Oplencu": {
    "sr": "Crkva Svetog Đorđa na Oplencu",
    "en": "Church of Saint George at Oplenac",
    "de": "St.-Georgs-Kirche auf dem Oplenac",
    "ru": "Церковь Святого Георгия на Опленце"
  },
  "Manastir Manasija": {
    "sr": "Manastir Manasija",
    "en": "Manasija Monastery",
    "de": "Kloster Manasija",
    "ru": "Монастырь Манасия"
  },
  "Lepenski Vir": {
    "sr": "Lepenski Vir",
    "en": "Lepenski Vir",
    "de": "Lepenski Vir",
    "ru": "Лепенски-Вир"
  },
  "Feliks Romulijana": {
    "sr": "Feliks Romulijana",
    "en": "Felix Romuliana",
    "de": "Felix Romuliana",
    "ru": "Феликс Ромулиана"
  },
  "Kućica na Drini": {
    "sr": "Kućica na Drini",
    "en": "House on the Drina",
    "de": "Haus auf der Drina",
    "ru": "Домик на Дрине"
  },
  "Manastir Blagoveštenje": {
    "sr": "Manastir Blagoveštenje",
    "en": "Blagoveštenje Monastery",
    "de": "Kloster Blagoveštenje",
    "ru": "Монастырь Благовештенье"
  },
  "Manastir Studenica": {
    "sr": "Manastir Studenica",
    "en": "Studenica Monastery",
    "de": "Kloster Studenica",
    "ru": "Монастырь Студеница"
  },
  "Crkva Lazarica": {
    "sr": "Crkva Lazarica",
    "en": "Lazarica Church",
    "de": "Lazarica-Kirche",
    "ru": "Церковь Лазарица"
  },
  "Niška tvrđava": {
    "sr": "Tvrđava sa Stambol kapijom",
    "en": "Fortress with the Istanbul Gate",
    "de": "Festung mit dem Istanbuler Tor",
    "ru": "Крепость со Стамбульскими воротами"
  },
  "Hisar u Prokuplju": {
    "sr": "Utvrđenje Hisar",
    "en": "Hisar fortification",
    "de": "Befestigung Hisar",
    "ru": "Укрепление Хисар"
  },
  "Pirotska tvrđava": {
    "sr": "Momčilov grad",
    "en": "Momčilo’s fortress",
    "de": "Momčilos Festung",
    "ru": "Крепость Момчило"
  },
  "Caričin grad": {
    "sr": "Caričin grad",
    "en": "Caričin Grad",
    "de": "Caričin Grad",
    "ru": "Царичин-Град"
  },
  "Beli most u Vranju": {
    "sr": "Most ljubavi",
    "en": "Bridge of Love",
    "de": "Brücke der Liebe",
    "ru": "Мост любви"
  },
  "Manastir Gračanica": {
    "sr": "Manastir kralja Milutina",
    "en": "King Milutin’s monastery",
    "de": "Kloster des Königs Milutin",
    "ru": "Монастырь короля Милутина"
  },
  "Pećka patrijaršija": {
    "sr": "Patrijaršijski manastir",
    "en": "Patriarchal monastery",
    "de": "Patriarchenkloster",
    "ru": "Патриарший монастырь"
  },
  "Kameni most u Prizrenu": {
    "sr": "Stari kameni most",
    "en": "Old Stone Bridge",
    "de": "Alte Steinbrücke",
    "ru": "Старый каменный мост"
  },
  "Manastir Banjska": {
    "sr": "Manastir Banjska",
    "en": "Banjska Monastery",
    "de": "Kloster Banjska",
    "ru": "Монастырь Баньска"
  },
  "Tvrđava Novo Brdo": {
    "sr": "Srednjovekovna rudarska tvrđava",
    "en": "Medieval mining fortress",
    "de": "Mittelalterliche Bergbaufestung",
    "ru": "Средневековая крепость рудокопов"
  }
}
SND_LANDMARK_LABELS
,true);
