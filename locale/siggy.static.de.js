//first key is wh class, second is just unique for mag in the class
var anomsLookup = {
	1: {
		0: "",
		1: "Randgebiet für Hinterhalt",
		2: "Lager im Randgebiet",
		3: "Phasenkatalysator-Knotenpunkt",
		4: "Das Feld"
	},
	2: {
		0: "",
		1: "Kontrollpunkt im Randgebiet",
		2: "Perimeter Hangar",
		3: "Die Ruinen von Enclave Cohort 27",
		4: "Datenheiligtum der Sleeper"
	},
	3: {
		0: "",
		1: "Festung im Grenzgebiet",
		2: "Bollwerk im Grenzgebiet",
		3: "Solarzelle",
		4: "Das Oruze-Konstrukt"
	},
	4: {
		0: "",
		1: "Kasernen im Grenzgebiet",
		2: "Kommandoposten im Grenzgebiet",
		3: "Endstation einbegriffen",
		4: "Informationsheiligtum der Sleeper"
	},
	5: {
		0: "",
		1: "Quarantänegebiet",
		2: "Wichtige Garnison",
		3: "Wichtige Festung",
		4: "Oruze Osobnyk"
	},
	6: {
		0: "",
		1: "Wichtige Zitadelle",
		2: "Wichtige Bastion",
		3: "Merkwürdige Energie-Messungen",
		4: "Der Spiegel"
	},
	7: {
		0: ""
	},
	8: {
		0: ""
	},
	9: {
		0: ""
	}
};


var magsLookup = {
	1: {
		0: "",
		1: "Vergessene Einsatzplattform im Außenbereich",
		2: "Vergessenes Energiefeld im Außenbereich"
	},
	2: {
		0: "",
		1: "Vergessenes Tor im Außenbereich",
		2: "Vergessene Wohnsiedlung im Außenbereich"
	},
	3: {
		0: "",
		1: "Vergessener Quarantäne-Außenposten im Grenzgebiet",
		2: "Vergessenes Rücklauf-Depot im Grenzgebiet"
	},
	4: {
		0: "",
		1: "Vergessenes Konversionsmodul im Grenzgebiet",
		2: "Vergessenes Evakuierungszentrum im Grenzgebiet"
	},
	5: {
		0: "",
		1: "Vergessenes wichtiges Datenfeld",
		2: "Vergessenes wichtiges Informationszentrum"
	},
	6: {
		0: "",
		1: "Vergessene wichtige Montageanlage",
		2: "Vergessener wichtiger Circuitry Disassembler"
	},
	7: {
		0: ""
	},
		8: {
	0: ""
	},
	9: {
		0: ""
	}
};

var radarsLookup = {
	1: {
		0: "",
		1: "Ungesicherter Verstärker im Außenbereich",
		2: "Ungesichertes Informationszentrum im Außenbereich"
	},
	2: {
		0: "",
		1: "Ungesichertes Kommunikationsrelais",
		2: "Ungesichertes Transponderfeld im Außenbereich"
	},
	3: {
		0: "",
		1: "Ungesicherte Datenbank im Grenzgebiet",
		2: "Ungesicherter Empfänger im Grenzgebiet"
	},
	4: {
		0: "",
		1: "Ungesicherter Digitalnexus im Grenzgebiet",
		2: "Ungesicherter Trinär-Knotenpunkt im Grenzgebiet"
	},
	5: {
		0: "",
		1: "Ungesicherte Grenzenklaven-Relaisstaion",
		2: "Ungesicherte Server-Datenbank im Grenzgebiet"
	},
	6: {
		0: "",
		1: "Ungesichertes wichtiges Datenfeld",
		2: "Ungesicherter wichtiger Ort"
	},
	7: {
		0: ""
	},
	8: {
		0: ""
	},
	9: {
		0: ""
	}
};

var gravsLookup = {
	0: "",
	1: "Gewöhnliche Erzvorkommen im Grenzgebiet",
	2: "Gewöhnliche Erzvorkommen im Grenzgebiet",
	3: "Gemeinschaftliche Erzvorkommen",
	4: "Besondere grundlegende Erzvorkommen",
	5: "Seltene Kernablagerung",
	6: "Ungewöhnliche Kernablagerung",
	7: "Rare Kernablagerung",
	8: "Gewöhnliche Erzvorkommen",
	9: "Ungewöhnliche Kernablagerung",
	10: "Vereinzelte grundlegende Erzvorkommen"
};

var ladarsLookup = {
	0: "",
	1: "Karges Erzgebiet",
	2: "Unwesentliches Erzgebiet",
	3: "Gewöhnliches Erzgebiet",
	4: "Beträchtliches Erzgebiet",
	5: "Anzeichen eines Erzgebietes",
	6: "Üppiges Erzgebiet",
	7: "Große Erzvorkommen im Grenzgebiet",
	8: "Nützliche grundlegende Erzvorkommen",
	9: "Wichtige grundlegende Erzvorkommen"
};