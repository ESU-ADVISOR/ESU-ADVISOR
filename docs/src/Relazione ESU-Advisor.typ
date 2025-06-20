#let project(
  title: "ESU ADVISOR",
  subtitle: "Relazione del Progetto",
  logo: "188218840.png",
  unilogo: "188218840.png",
  primary-color: rgb("#00b0ff"),
  body
) = {

  let secondary-color = color.mix(color.rgb(100%, 100%, 100%,60%), primary-color, space:rgb)
  set page(
    header: [
      // Header con titolo del progetto
      #grid(
      columns: (33%, 34%, 33%),
      align(left)[#text(title)],
      [],
      align(right)[#text(subtitle)]
      )
      #line(length: 100%, stroke: 0.5pt)
    ],
    footer: [
      #line(length: 100%, stroke: 0.5pt)
      // Footer con numero di pagina
      #text(size: 10pt, align(center)[#context[ #text[#counter(page).display("1")/#counter(page).final().at(0)]]])
    ],
    margin: (top: 2cm, bottom: 2cm, left: 2cm, right: 2cm)
  )
  set heading(numbering: "1.")

  // Forme geometriche decorative ai lati
  page( header: [], footer: [])[
    #set text(
      size: 14pt,
    )

    #place(top + left, dx: -35%, dy: -28%, circle(radius: 150pt, fill: primary-color))
    #place(top + left, dx: -10%, circle(radius: 75pt, fill: secondary-color))

    // decorations at bottom right
    #place(bottom +right, dx: 40%, dy: 30%, circle(radius: 150pt, fill: secondary-color))

    #align(end)[#image("img/unipd.jpg", width: 4cm, height:4cm)]
    // Logo del progetto centrato sopra il titolo
    #align(center)[#image("img/favicon.png", width: 4cm, )]

    #align(center)[
      #text(size: 36pt, weight: "bold")[#title]
            #linebreak()
      #linebreak()
      #text(size: 24pt)[#subtitle]
      #linebreak()
      #linebreak()
      #text()[*#datetime.today().display()*]
      #align(center)[
        #table(
          columns: 2,
          stroke: none,
          align(left)[*Sede*], align(left)[Università degli Studi di Padova],
          align(left)[*Facoltà*], align(left)[Informatica],
          align(left)[*Corso*], align(left)[Tecnologie Web],
          align(left)[*Anno*], align(left)[2024/25]
        )
      ]
      #v(0.3cm)
      #text(size: 13pt)[
        *Indirizzo web:* http://tecweb.studenti.math.unipd.it/mvasquez/index.php
      ]
      #v(0.3cm)
      #text()[*A cura di*]
      #align(center)[
        #table(
          columns: 2,
          stroke: none,
          align(left)[*Membro*], align(left)[*Matricola*],
          align(left)[Giacomo Loat], align(left)[2077677],
          align(left)[Giulio Bottacin], align(left)[2042340],
          align(left)[Malik Giafar Mohamed], align(left)[2075543],
          align(left)[Manuel Felipe Vasquez], align(left)[2076425]
        )
      ]
      #v(0.5cm)
      #text(size: 12pt)[
        *Credenziali utente:* user / user \
        *Email referente:* manuelfelipe.vasquez\@studenti.unipd.it
      ]
    ]
  ]
  page()[
    #outline(title: "Indice")
  ]
  body
}

#show: project

= Introduzione

== Obiettivo del Progetto
ESU-Advisor è una piattaforma web dedicata agli studenti universitari di Padova che permette di consultare i menu delle mense universitarie dell'ESU (Azienda Regionale per il Diritto allo Studio Universitario) e di lasciare recensioni sui piatti disponibili. Il progetto nasce dall'esigenza di fornire agli studenti uno strumento semplice e accessibile per valutare e condividere informazioni sui pasti offerti nelle diverse mense universitarie.

== Composizione del Team
Il progetto è stato realizzato da un gruppo di quattro studenti con competenze complementari:

#figure(
  caption: [Membri del team e relativi ruoli],
  table(
    columns: 4,
    [*Nome*], [*Cognome*], [*Matricola*], [*Competenze Principali*],
    [Giacomo], [Loat], [2077677], [HTML5, Design UI/UX, Struttura Frontend],
    [Giulio], [Bottacin], [2042340], [CSS3, Responsive Design, Accessibilità],
    [Malik], [Giafar Mohamed], [2075543], [Database Design, SQL, Architettura Dati],
    [Manuel Felipe], [Vasquez], [2076425], [PHP, Backend Development]
  )
)

== Analisi degli Utenti Target
=== Caratteristiche Primarie degli Utenti
Il pubblico di riferimento è costituito principalmente da studenti universitari di Padova, con un'età compresa tra 18 e 30 anni, che fanno un uso frequente di dispositivi mobili.  Si è tenuto conto anche delle loro possibili esigenze alimentari specifiche (come allergie o intolleranze).

=== Possibili Ricerche sui Motori di Ricerca
- "mense universitarie Padova";
- "menu mensa ESU oggi";
- "recensioni cibo mense Padova";
- "orari mense università Padova";
- "piatti del giorno mense universitarie".

== Suddivisione del Lavoro

=== Componente di Contenuto (Giacomo Loat)
- Sviluppo della struttura HTML5;
- Implementazione dei template delle pagine;
- Integrazione dei contenuti dinamici;
- Ottimizzazione per SEO.

=== Componente di Presentazione (Giulio Bottacin)
- Implementazione dello stile CSS;
- Creazione delle componenti UI riutilizzabili;
- Implementazione del design responsive;
- Sviluppo delle feature di accessibilità (WCAG 2.0);
- Gestione dei temi chiaro/scuro;
- Supporto per utenti con dislessia.
#pagebreak()
=== Componente di Comportamento (Manuel Felipe Vasquez)
- Sviluppo della logica JavaScript client-side;
- Implementazione delle validazioni form;
- Architettura MVC backend in PHP;
- Gestione dell'autenticazione e autorizzazione;
- Sviluppo delle API REST.

=== Struttura Database (Malik Giafar Mohamed)
- Design dello schema del database normalizzato;
- Implementazione delle relazioni tra entità;
- Ottimizzazione delle query;
- Implementazione del sistema di gestione allergeni.

== Metodologia di Collaborazione

=== Strumenti e Workflow
- *Git* per il controllo versione distribuito;
- *GitHub* per la gestione del repository e code review;
- *Docker* per lo sviluppo containerizzato senza prerequisiti di sistema;
- *Visual Studio Code* come IDE condiviso con Live Share.

= Progettazione

== Schema organizzativo
Poiché la struttura del sito web varia solo nel contenuto del tag `<main>`, mantenendo pressoché invariata la struttura composta da header e sidebar/footer, si può dire che lo schema organizzativo ambiguo scelto per il raggruppamento dei link alle pagine principali si applica a tutto il sito web.

Nel sito web infatti, sarà sempre presente una sidebar a sinistra (desktop), o un footer in basso (mobile), che contiene i link alle pagine principali del sito, le quali sono:
- *Home*: pagina principale dove è possibile seleziona una mensa tra quelle disponibili e visualizzare i piatti offerti nel menu del giorno e le loro recensioni;
- *Review*: pagina per l'inserimento di recensioni sui piatti;
- *Profilo*: pagina del profilo utente per visualizzare le informazioni personali e le recensioni;
- *Impostazioni*: pagina per la gestione di preferenze alimentari, impostazioni d'accessibilità, modifica del username e password, e la possibilità di cancellare l'account.

== Schema a tre pannelli
Per ridurre il fenomeno del disorientamento dell'utente, è stato scelto di adottare lo schema a tre pannelli, adattando il design del sito per rispondere a queste tre domande:
- *Dove sono?*
  - L'utente può orientarsi grazie all'evidenziazione della pagina corrente nella sidebar (o l'icona evidenziata nel footer su mobile), oppure tramite la breadcrumb presente nell'header.
- *Dove posso andare?*
  - L'utente può navigare tra le pagine del sito tramite la sidebar/footer, che mostra le sezioni principali del sito web. \ Sono inoltre presenti dei link all'interno del contenuto del sito che portano alla visualizzazione dei piatti del giorno, rispondendo alla domanda *_Cosa posso fare?_*.
#pagebreak()
- *Di cosa si tratta?*
 - Ogni azione intraprendibile è descritta in modo chiaro e conciso, con l'eventuale uso di icone e testi esplicativi per facilitare la comprensione. Alcuni esempi sono i link "vedi le recensioni" presenti nelle schede dei piatti della homepage.

Il sito inoltre risponde alla domanda *Dove posso trovare altre informazioni?* mettendole a disposizione nella homepage o esplicitandole in dei link (come ad esempio il link di Maps per la mensa).

== Convenzioni interne
Per garantire coerenza, usabilità e orientamento all'utente, sono state adottate delle convenzioni interne, che si riflettono in tutte le pagine dell'applicazione:
- *Stile bottoni:* sono stati definiti tre stili di bottone al fine di indicare a prima vista i loro ruoli:
  - Bottone principale: sempre blu con testo bianco;
  - Bottone secondario: testo blu su sfondo chiaro o scuro a seconda del tema e bordo blu;
  - Bottoni di pericolo: sempre arancioni con testo bianco.
  Tutti gli stili rispettano i corretti contrasti tra testo, corpo del pulsante e sfondo per garantire accessibilità.
- *Stile dei link:* lo stile dei link è stato mantenuto molto simile a quello standard ma la tonalità dei colori (blu per i link non visitati, viola per i link visitati) è stata adattata alla palette cromatica del sito per ottenere una buona resa estetica e corretti contrasti tra i colori;
- *Struttura a Card:* le varie sezioni delle pagine e i loro elementi sono illustrati con uno stile ripetuto, al fine di rendere più semplice e intuitiva la struttura secondo la quale i contenuti sono disposti all'interno del sito;
- *Navigazione adattiva:* la disposizione degli elementi di navigazione varia in base al dispositivo utilizzato. Su dispositivi mobili, il menu principale è posizionato in un footer fisso in basso, mentre i pulsanti di accesso, registrazione e logout sono collocati in alto a destra. Su desktop, invece, tutte le funzioni di navigazione sono raccolte in una sidebar laterale a sinistra, ottimizzando lo spazio e rendendo più intuitivo l'accesso alle diverse sezioni;
- *Assenza di link circolari:* per evitare di creare un link circolare, i relativi pulsanti della pagina corrente vengono disabilitati automaticamente
- *Istruzioni di compilazione nei form:* dove necessario, i campi dei form sono preceduti da istruzioni di compilazione che spiegano cosa inserire e indicano i requisiti del campo;
- *Messaggi di errore nei form:* per ridurre la probabilità che un utente debba compilare e inviare un modulo più volte, i messaggi di errore vengono mostrati sotto i rispettivi campi già con l'evento “blur” (fatta eccezione per gli errori che devono essere verificati lato server).

== Layout
In base a quanto dedotto nella sezione di analisi delle utenze, è stato deciso adottare un *approccio mobile-first* nella progettazione dell'interfaccia utente.
Questo non significa soltanto aver adottato un *layout responsive*, ma significa che il design e lo sviluppo dell'applicazione sono stati inizialmente focalizzati sulle esigenze degli utenti che utilizzano dispositivi mobili, siccome questi saranno coloro che utilizzeranno maggiormente il sito web.
#pagebreak()
== Funzionalità
Il sito web offre le seguenti funzionalità principali:
=== Autenticazione
Gli utenti possono registrarsi e accedere al sito per usufruire di funzionalità avanzate, come la possibilità di lasciare recensioni sui piatti e personalizzare le proprie preferenze alimentari.
=== Visualizzazione piatti del giorno
Gli utenti possono visualizzare i piatti del giorno delle mense universitarie, con dettagli su ingredienti, allergeni e recensioni degli utenti.
=== Recensioni
Gli utenti registrati possono lasciare recensioni sui piatti, valutandoli e fornendo commenti che possono tornare utili ad altri utenti.
=== Preferenze alimentari
Gli utenti possono impostare preferenze alimentari specifiche, come allergie o intolleranze, per ricevere avvisi sui piatti che potrebbero contenere ingredienti indesiderati.
=== Personalizzazione impostazioni
Nella pagina delle impostazioni, gli utenti sia autenticati che non possono personalizzare la propria esperienza di navigazione, in particolare, è possibile:
- Attivare la *modalità scura* per ridurre l'affaticamento visivo, se il dispositivo o il browser ha la preferenza per il tema scuro il sito apparirà in tal modo automaticamente;
- Modificare le *dimensioni del testo* per una lettura più confortevole;
- Cambiare il font in uno specifico font per la *dislessia* per migliorare la leggibilità del testo.

Il font per la dislessia scelto è stato "OpenDyslexic", un font open source progettato specificamente per facilitare la lettura dei contenuti testuali presenti nel sito.

Inoltre, sempre nella stessa pagina è possibile impostare una mensa preferita, che verrà mostrata subito nella home senza necessità di selezionarla ogni volta.

== Note
Le immagini presenti nel sito sono state prese dagli smartphone dei membri del gruppo e dei loro conoscenti, mentre in alcuni casi, siccome non erano disponibili delle foto dove venisse mostrato il piatto singolo, abbiamo ricorso alla generazione delle foto con l'intelligenza artificiale.

Inoltre, per quanto riguarda i menu, la disposizione dei piatti nel sito non rispecchia quella delle mense reali.
#pagebreak()
= Implementazione

== Struttura delle Directory <struttura>
Il progetto è organizzato con una netta separazione tra i file accessibili pubblicamente (public-html), che includono pagine PHP, stili, script e risorse, e il codice sorgente del backend (src), che contiene i file MVC e di configurazione, non esposti direttamente sul web.

```bash
ESU-ADVISOR/
├── docs/                   # Documentazione progetto
│
├── public-html/            # File esposti pubblicamente accessibili
│   ├── styles/             # Fogli di stile modulari
│   ├── scripts/            # Script client-side
│   ├── images/             # Risorse grafiche
│   ├── fonts/              # Cartella per i font
│   ├── *.php               # Pagine dell'applicazione
│   ├── .htaccess           # File per la risoluzione delle path del server APACHE
│   ├── robots.txt          # File per indicare ai crawler le pagine che possono visitare
│   └── index.php           # Entry point applicazione
│
├── src/                    # File utilizzati lato backend non ottenibili dal webserver
│   ├── controllers/        # Controller MVC
│   ├── models/             # Model MVC
│   ├── templates/          # File HTML puri contenenti la struttura della pagina
│   ├── views/              # View MVC
│   ├── config.php          # File per le variabili d'ambiente per l'accesso al DB
│   └── session_init.php    # File per le impostazioni della sessione di PHP
│
├── db.sql                  # Schema database e query di inserimento dati iniziali
└── README.md
```

== Backend PHP
Tutte le richieste che vengono effettuate al sito web vengono gestite da file PHP present in public_html, come `index.php`, `register.php`, `login.php`, `settings.php`, etc., che fungono da entry point per l'applicazione. Questi file, oltre a validare i dati inseriti dell'utente lato server, si occupano di caricare le configurazioni necessarie, inizializzare la sessione e instradare le richieste agli appropriati controller MVC in base all'URL richiesto e al metodo utilizzato (GET/POST).

=== Pattern Architetturale MVC
Il progetto implementa rigorosamente il pattern Model-View-Controller (MVC) per garantire modularità e manutenibilità, dove:

==== Model Layer
Gestisce la logica dei dati e le interazioni con il database:
- *DatabaseModel*: gestisce l'accesso al database;
- *MenseModel*: rappresenta le mense universitarie e le relative informazioni;
- *MenuModel*: rappresenta le i menu delle mense;
- *PiattoModel*: rappresenta un piatto di un singolo menu;
- *PreferenzeUtenteModel*: l'insieme delle impostazioni di personalizzazione esperienza utente;
- *RecensioneModel*: rappresenta una singola valutazione;
- *UserModel*: rappresenta un utente.
#pagebreak()
==== View Layer
Si occupa della presentazione dei dati attraverso un sistema di template che separa il contenuto dalla formattazione:
- *BaseView*: classe di base da cui ogni view eredita le funzionalità;
- *ErrorView*: gestisce le pagine di errore;
- *IndexView*: homepage con selezione mense;
- *LoginView* / *RegisterView*: mostra i form di autenticazione o registrazione;
- *PiattoView*: mostra i dettagli di un piatto piatto e le sue recensioni;
- *ProfileView*: mostra i dettagli di un profilo utente;
- *ReviewView*: mostra i dettagli di una recensione;
- *ReviewEditView*: mostra un form per modificare una recensione;
- *SettingsView*: mostra la pagina delle impostazioni.

==== Controller Layer
Gestisce le richieste dell'utente, dialoga con il Model e seleziona la View appropriata da mostrare con i dati appropriati:
- *BaseController*: classe di base da cui ogni controller eredita le funzionalità;
- *ErrorController*: gestisce le pagine di errore;
- *IndexController*: gestisce la visualizzazione dei piatti e delle informazioni di una mensa;
- *LoginController* / *RegisterController*: gestiscono l'autenticazione e la registrazione degli utenti nel sito web;
- *PiattoController*: gestisce la visualizzazione dei singoli piatti presenti nel database;
- *ProfileController*: gestisce la visualizzazione delle informazioni sul profilo utente;
- *ReviewController*: si occupa dell'inserimento e modifica delle recensioni di un utente;
- *ReviewEditController*: un controller più specifico che gestisce il form di modifica di una recensione;
- *SettingsController*: si occupa della gestione delle impostazioni del sito web.

=== Tecnica di Templating
Per separare la struttura dal comportamento abbiamo fatto uso del tag *`<template>`* fornito da HTML per evitare di inserire snippet di PHP all'interno della struttura del documento. Per ogni tag viene aggiunto un id univoco con il formato *`nome-contenuto-template`*. Poi per inserire i dati relativi si è fatto uso della funzione *`replaceTemplateContent($dom, $templateId, $newContent)`* presente nel file `Utils.php`, dove:
- *`$dom`* contiene il testo dell'intero file html parsabile contenente i tag *`<template>`*;
- *`$templateId`* l'id del tag *`<template>`* che si vuole sostituire;
- *`$newContent`* il contenuto in formato HTML da inserire.

Per effettuare il parsing del file e ottenere la posizione del tag è stata utilizzata la classe *`DOMDocument`* di PHP, che fornisce una serie di funzioni per gestire e manipolare i file HTML. Nel nostro caso specifico utilizziamo la classe per controllare che il contenuto inserito sia formattato correttamente con caratteri utf-8 e che rispetti la sintassi HTML.

Questa funzione viene utilizzata spesso per gestire tutte le varie componenti del sito che richiedono dati provenienti dal DB o che richiedono di essere processate, ad esempio viene utilizzata per modularizzare la struttura della pagina, in particolare ogni View eredita dalla classe *`BaseView`* che espone il metodo *`render()`* che di default inserisce le componenti *`header`*, *`footer`* e *`sidebar`* in ogni file html che viene richiesto affinché esistano i tag template corrispondenti. Questo permette di separare la struttura di componenti ripetute in file unici favorendo la modularità del sito e permettendo a più componenti del gruppo di lavorare sul progetto evitando il più possibile di avere conflitti nelle modifiche.
#pagebreak()
== CSS
Per il sito è stato utilizzato CSS3 puro, e per una migliore gestione del layout delle pagine sono state definite delle variabili globali per rendere coerenti le proprietà dei vari elementi, come colore dei bottoni, il testo, background di elementi ripetuti, spaziature, font, e così via. Inoltre vi è presente la variazione delle stesse variabili per il tema scuro, che verranno applicate in base in base alle preferenze dell'utente o del suo sistema operativo.

È stato anche implementato un sistema di *media queries* per garantire che il sito sia responsive e favorisca la fruizione dei contenuti del sito su più dispositivi con diverse dimensioni di schermo.

== Scripts Javascript e Validazione dell'input lato Client
Sono stati utilizzati script javascript per gestire il comportamento del sito, in particolare:
- *Validazione dell'input dei form lato client*: non strettamente necessaria in quanto vi è l'equivalente validazione a lato server per motivi di sicurezza, ma aiuta l'utente ad inserire correttamente i dati richiesti prima di ritrovarsi con una pagina d'errore;

- *Funzionalità di ricerca dei piatti nel menù*: per favorire la ricerca di alimenti o allergeni tra i piatti della mensa attualmente selezionata in maniera veloce, vi è implementata una barra di ricerca in cima alla sezione del menu del giorno, che filtrerà i piatti presenti nascondendo quelli che non corrispondono ai termini di ricerca;
- *Suggerimenti piatti durante la recensione*: per facilitare il processo di recensione dei piatti, una volta selezionata la mensa, al momento della scrittura del nome del piatto, verrà fornita una lista dei piatti di cui la mensa dispone al fine di evitare problemi di spelling del nome del piatto. Questa scelta è preferibile rispetto ad avere soltanto una lista, come nel caso della selezione della mensa, per rendere più semplice e veloce l'inserimento del nome del piatto qualora il menù della mensa contenesse numerose opzioni.

Il sito web inoltre può funzionare correttamente pur non avendo abilitato javascript nel browser. In tal caso verrà mostrato un avviso che inviterà l'utente ad abilitarlo.

== Gestione degli errori di navigazione
Nel caso si volesse fare accesso a link che puntano a risorse inesistenti or malformati, come ad esempio `http://<server>/index.php/wrong/path` o `http://<server>/wrong-filename.php`, sono state definite delle regole nel file `public_html/.htaccess` per gestire queste condizioni. In particolare se un utente volesse visitare una pagina che non esiste o che non può visitare senza un account (ad esempio `review.php` e `profile.php`), essi verrebbero reindirizzati alla pagina `error.php` dove in base alle condizioni del reindirizzamento verranno indicate informazioni utili all'utente per comprendere cosa è appena successo e ad indicargli cosa dovrebbe fare, nell'esempio corrente gli indicherà che "È necessario effettuare l'accesso per visualizzare questa pagina", con sotto i pulsanti per effettuare l'accesso o la registrazione se l'utente non ha già un account.

Queste regole sono state scritte per supportare deployment del sito web sia nel caso in cui sia presente nel root del server (`http://<server>/index.php`) o che si trovi in una sottocartella, come nel caso del server universitario (`http://<server>/<name>/index.php`).

== Database Design

=== Schema Relazionale Normalizzato
Lo schema è normalizzato fino alla Terza Forma Normale (3NF) per minimizzare la ridondanza dei dati e prevenire anomalie di inserimento, aggiornamento e cancellazione.

#figure(
  caption: [Tabelle principali del database],
  table(
    columns: 3,
    [*Tabella*], [*Scopo*], [*Relazioni*],
    [mensa], [Informazioni mense], [1:N con orarioapertura, menu],
    [piatto], [Catalogo piatti], [1:N con recensione, menu, piatto_foto],
    [utente], [Gestione utenti], [1:N con recensione, 1:1 con preferenze_utente],
    [orarioapertura], [Orari mense], [-],
    [menu], [Associazione piatti-mense], [N:N tra piatto e mensa],
    [recensione], [Sistema valutazioni], [N:1 con utente, piatto],
    [piatto_foto], [Associazione piatti-foto], [-],
    [piatto_allergeni], [Gestione allergeni], [N:N tra piatto e allergeni],
    [preferenze_utente], [Personalizzazione], [1:1 con utente],
    [allergeni_utente], [Personalizzazione], [1:1 con utente]
  )
)

#figure(
  image("img/DBER.png", width: 100%, height: auto, alt: "Schema ER del database ESU-Advisor"),
  caption: [Schema ER del database ESU-Advisor]
)

Lo schema si articola attorno a tre entità fondamentali: le *mense*, i *piatti* e gli *utenti*.
- *mensa:* questa tabella contiene le informazioni anagrafiche di ogni mensa universitaria. Il nome della mensa funge da chiave primaria e identificatore univoco. Gli altri campi includono l'indirizzo, il numero di telefono e un link esterno a Google Maps per la localizzazione geografica;
- *piatto:* memorizza il catalogo di tutti i piatti che possono essere serviti. Ogni piatto è identificato univocamente dal suo nome (chiave primaria) e appartiene a una categoria predefinita ("Primo", "Secondo", "Contorno"). Contiene inoltre una descrizione testuale;
- *utente:* gestisce i dati degli utenti registrati alla piattaforma. Ogni utente ha un id numerico auto-incrementale come chiave primaria e uno username univoco. La tabella memorizza anche la password (che verrà sottoposta ad hashing prima dell'inserimento) e la data di nascita.

Le entità principali sono collegate tra loro e arricchite di dettagli attraverso una serie di tabelle satellite.
- *orarioapertura:* definisce gli orari di apertura settimanali per ciascuna mensa, creando una relazione uno-a-molti con la tabella mensa. Un vincolo di CHECK assicura che il `giornoSettimana` sia un valore compreso tra 1 e 7;
- *menu:* si tratta di una tabella di collegamento che implementa una relazione molti-a-molti tra piatto e mensa, specificando quali piatti sono disponibili in quali mense;
- *recensione:* è la tabella centrale per le interazioni degli utenti. Stabilisce una relazione tra un utente, un piatto e una mensa. Un utente può lasciare una sola recensione per un dato piatto in una specifica mensa. La recensione include un voto (da 1 a 5), una descrizione testuale opzionale e la data di inserimento. La chiave esterna composta (piatto, mensa) garantisce che una recensione possa essere lasciata solo per un piatto effettivamente presente nel menu di quella mensa.


Infine, sono presenti tabelle per gestire informazioni aggiuntive sui piatti e le preferenze degli utenti.
  - *piatto_foto* e *piatto_allergeni:* queste due tabelle aggiungono dettagli ai piatti. La prima gestisce l'associazione uno-a-molti tra un piatto e le sue foto (salvando il percorso all'immagine nel db). La seconda definisce una relazione molti-a-molti per associare a ogni piatto uno o più allergene da una lista predefinita conforme alla normativa europea;
  - *preferenze_utente* e *allergeni_utente:* queste tabelle sono dedicate alla personalizzazione dell'esperienza utente. `preferenze_utente` ha una relazione uno-a-uno con utente e memorizza impostazioni di accessibilità come la `dimensione_testo`, l'uso del font per la dislessia (`modifica_font`), il `modifica_tema` visivo e la `mensa_preferita`. `allergeni_utente`, invece, permette agli utenti di registrare i propri allergeni personali in una relazione molti-a-molti, per ricevere avvisi mirati.

== SEO e Performance
Al fine di ottimizzare il posizionamento del sito nei motori di ricerca, sono state implementate le seguenti strategie:
- *Definizione di Meta tags*: sono stati creati meta tag specifici per ogni pagina, inclusi titolo e descrizione per ogni pagina del sito web;
- *Sitemap XML*: è stata creata una sitemap per facilitare l'indicizzazione da parte dei motori di ricerca, che include tutte le pagine principali del sito. La sitemap viene generata automaticamente dallo script `sitemap.php` presente nella cartella `public_html/`, e viene aggiornata includendo ogni pagina del sito web, al momento dell'accesso;
- *URLs SEO-friendly*: ogni URL del sito web è stato progettato per essere descrittivo e contenere parole chiave pertinenti, evitando di utilizzare parametri complessi o identificatori numerici. Ad esempio, l'URL per la pagina di recensioni di un piatto specifico è strutturato come `http://<server>/review.php?piatto=<nome-piatto>&mensa=<nome-mensa>`, dove `<nome-piatto>` e `<nome-mensa>` sono i nomi dei piatti e delle mense rispettivamente.

#pagebreak()

= Accessibilità

== Personalizzazione Accessibilità
Le impostazioni di accessibilità possono essere gestite sia dagli utenti registrati che non. Esse verranno salvate all'interno della sessione di PHP, e nel caso tali fossero salvate per un utente registrato, una volta effettuato l'accesso esse verranno applicate automaticamente. Le impostazioni disponibili sono:
- *Dimensioni testo*: piccolo, medio, grande;
- *Font per dislessia*: OpenDyslexic font selezionabile dalle impostazioni;
- *Temi*: chiaro, scuro, sistema (prende l'impostazione dal tema del browser), questo viene caricato prima che l'utente possa visualizzare la pagina per evitare flashing.

== Navigazione accessibile
Vengono riportati qui sotto altri aspetti che rendono questo sito accessibile a tutte le tipologie di utenti:

- *Navigazione da Tastiera*: il sito è interamente utilizzabile tramite la tastiera. Gli elementi non interagibili della pagina (come la mensa e il nome del piatto durante la modifica di una recensione) vengono nascosti. Inoltre sono presenti dei link di salto rapido ("Vai al contenuto" e "Vai alla navigazione"), per facilitare la navigazione e migliorare l'esperienza per chi utilizza uno screen reader;

- *Supporto per gli screen reader*: sono state utilizzate classi CSS per migliorare l'accessibilità del sito per gli utenti che necessitano l'uso di uno screen reader, dove tali applicano descrizioni e informazioni utili soprattutto riguardo alle immagini dei piatti presenti nel sito e le stelle di valutazione nella pagina per la review, dove ad ogni stella vi è indicata il numero e il significato della valutazione;

- *Tabelle accessibili*: ogni tabella del sito web è stata resa accessibile tramite:
  - *Attributi di scoping*: Ogni tabella ha un'intestazione chiara e descrittiva per ogni colonna, aiutando gli utenti a comprendere la struttura e il contenuto della tabella;

  - *Tag di accessibilità*: lo scopo principale delle tabelle nel sito è quello di mostrare gli orari di apertura delle mense di Padova, quindi sono stati utilizzati i tag `<abbr>` per abbreviare i nomi dei giorni della settimana, ad esempio "Lun" per "Lunedì", "Mar" per "Martedì", etc. Questo aiuta gli utenti a comprendere rapidamente il significato delle abbreviazioni. Inoltre, è stato utilizzato il tag `<time>` per indicare gli orari di apertura e chiusura delle mense, in modo che gli screen reader possano leggere correttamente le informazioni temporali;

  - *Descrizione accessibile e caption*: ogni tabella ha una caption che descrive il suo contenuto e scopo, migliorando l'accessibilità per gli utenti di screen reader. È presente inoltre l'attributo `aria-describedby` per fornire una descrizione aggiuntiva della tabella, che viene letta dagli screen reader per fornire ulteriori informazioni sul contenuto della tabella.

- *Assenza di Link Circolari*: non sono presenti link circolari o che portano a pagine senza contenuto utile, per evitare confusione e disorientamento dell'utente;

- *Form accessibili*: i form di inserimento dati sono stati progettati per essere accessibili, con etichette chiare e descrittive per ogni campo. Sono stati utilizzati attributi ARIA per migliorare l'accessibilità dei campi di input e dei pulsanti di invio;

- *Utilizzo di breadcrumbs*: è stata implementata una breadcrumb che mostra il percorso di navigazione dell'utente all'interno del sito, facilitando l'orientamento e la comprensione della struttura delle informazioni;

- *Contrasto e Colori:* i colori utilizzati per il tema chiaro/scuro sono stati scelti al fine di garantire un adeguato rapporto di contrasto tra i vari elementi presenti nel sito al fine di rispettale almeno le normative dello standard *WCAG 2.1 AA*.

== Tabelle di Contrasto
In queste tabelle sono riportati i colori utilizzati nel sito web, sia per la modalità chiara che per quella scura, con i rispettivi rapporti di contrasto tra testo e sfondo. I valori sono stati calcolati utilizzando il servizio online https://polypane.app/color-contrast, gli strumenti di accessibilità di Firefox e Chrome, e sono conformi agli standard WCAG 2.1 AA.

Per i casi in cui il colore di uno sfondo è semi-trasparente, e stato concatenato con il simbolo `+` il colore dello sfondo sotto quello semi-trasparente per indicare che il contrasto è stato calcolato considerando la sovrapposizione dei colori.

=== Modalità Chiara (White-Mode)

==== Header
#table(
  columns: 3,
  [*Testo*], [*Sfondo*], [*Rapporto di Contrasto*],
  [`--primary: #006ca8`], [`--background: #FFFFFF`], [5.66:1],
  [`--text-secondary: #333333`], [`--background: #FFFFFF`], [12.63:1]
)

==== Sidebar (testo sulla pagina corrente)
#table(
  columns: 3,
  [*Testo*], [*Sfondo*], [*Rapporto di Contrasto*],
  [`--primary: #006ca8`], [`--primary-15: #006ca826` + `--background: #FFFFFF`], [4.55:1]
)

==== Pulsanti
#table(
  columns: 3,
  [*Testo*], [*Sfondo*], [*Rapporto di Contrasto*],
  [`--text-on-primary: #ffffff`], [`--primary-button: #006BD2`], [5.21:1],
  [`--text-secondary: #006ca8`], [`--background: #FFFFFF`], [5.65:1],
  [`--text-on-danger: #ffffff`], [`--danger: #c25600`], [4.54:1]
)

==== Link
#table(
  columns: 3,
  [*Testo*], [*Sfondo*], [*Rapporto di Contrasto*],
  [`(non-visitato) --primary: #0044ff`], [`--background: #FFFFFF`], [6.42:1],
  [`(visitato) --visited: #6d2ed0`], [`--background: #FFFFFF`], [7.11:1]
)

==== Messaggio di Successo, Errore e Allergeni Evidenziati
#table(
  columns: 3,
  [*Testo*], [*Sfondo*], [*Rapporto di Contrasto*],
  [`--success: #025e40`], [`--success-background: #10b9811a` + `--background: #ffffff`], [7.14:1],
  [`--danger: #a52c00`], [`--background: #FFFFFF`], [7.09:1],
  [`--warning: #bb0202`], [`warning-background: #fdd50073` + `--background: #FFFFFF`], [5.63:1]
)

=== Modalità Scura (Dark-Mode)

==== Header
#table(
  columns: 3,
  [*Testo*], [*Sfondo*], [*Rapporto di Contrasto*],
  [`--primary: #60C6FF`], [`--background: #1B1B1B`], [9.03:1],
  [`--text-secondary: #EFEFEF`], [`--background: #1B1B1B`], [14.97:1]
)

===== Sidebar (testo sulla pagina corrente)
#table(
  columns: 3,
  [*Testo*], [*Sfondo*], [*Rapporto di Contrasto*],
  [`--primary: #60C6FF`], [`--primary-15: #006ca826` + `--background: #1B1B1B`], [8.15:1]
)

==== Pulsanti
#table(
  columns: 3,
  [*Testo*], [*Sfondo*], [*Rapporto di Contrasto*],
  [`--text-on-primary: #FFFFFF`], [`--primary-button: #006BD2`], [5.21:1],
  [`--primary: #60C6FF`], [`--background: #1B1B1B`], [9.03:1],
  [`--text-on-danger: #FFFFFF`], [`--danger: #a74b02`], [5.75:1]
)

==== Link
#table(
  columns: 3,
  [*Testo*], [*Sfondo*], [*Rapporto di Contrasto*],
  [`(non-visitato) --primary: #20afff`], [`--background: #1B1B1B`], [7.08:1],
  [`(visitato) --visited: #b793ff`], [`--background: #1B1B1B`], [7.08:1]
)

==== Messaggio di Successo, Errore e Allergeni Evidenziati
#table(
  columns: 3,
  [*Testo*], [*Sfondo*], [*Rapporto di Contrasto*],
  [`#04cc8b`], [`--success-background: #10b9811a` + `--background: #1B1B1B`], [7.06:1],
  [`--danger: #ff7507`], [`--background: #1B1B1B`], [6.40:1],
  [`--warning: #fb9c00`], [`warning-background: #7d60561a` SOPRA `--background: #1B1B1B`], [7.41:1]
)

= Testing e Validazione
Nei paragrafi successivi sono riportati tutti i test di accessibilità effettuati per il sito web ESU-ADVISOR, includendo gli strumenti utilizzati per la validazione.

== Ambiente di Sviluppo
Per avere un ambiente di sviluppo unico tra i membri del gruppo è stato utilizzato *Docker* per eseguire il codice sviluppato in un ambiente il più simile a quello del server del laboratorio, abbiamo utilizzato in particolare:
- Apache con PHP 8.2 (stessa del server universitario, diversamente da quanto inizialmente scritto nella consegna del progetto detta a lezione, ossia 8.1);
- MariaDB 10.6.7;
- PHPMyAdmin come strumento di utility per visualizzare il database.

Periodicamente durante il periodo di sviluppo del progetto il gruppo ha provato il sito anche sul server universitario per confermare la sua corretta funzionalità

== Strumenti
Qui sono riportati vari strumenti utilizzati al fine di convalidare l'accessibilità del sito:

=== Strumenti per L'accessibilità
- *WAVE* (Web Accessibility Evaluation Tool);
- *axe-core* accessibility checker;
- *Screen reader testing* con NVDA;
- *Keyboard navigation testing* completo;
- *Total Validator* per individuare possibili problemi su ogni pagina del sito;
- *Lighthouse* per convalidare la performance, accessibilità, best practice e SEO;
- *Suite d'accessibilità di Chrome & Firefox* per assicurarsi che i browser riconoscessero le nostre misure al fine di assicurarsi l'utilizzo del sito da qualsiasi categoria d'utente.

=== Validazione HTML
- *W3C Markup Validator*: 0 errori, 0 warning;
- *HTML5 semantic validation*: Markup semanticamente corretto;
- *XML compliance*: sintassi XML valida.

=== Validazione CSS
- *W3C CSS Validator*: CSS3 valido;
- *Cross-browser compatibility*: Chrome, Firefox, Safari, Edge;
- *Mobile compatibility*: Android, iOS.

== Browser Testing
Il sito è stato testato sui seguenti browser/dispositivi:
- Chrome 120+ (desktop/mobile);
- Firefox 121+ (desktop/mobile);
- Safari 17+ (desktop/mobile);
- Edge 120+ (desktop).

= Istruzioni per l'installazione del progetto
Per installare il progetto in locale in un ambiente simile a quello del laboratorio (*Apache* con *PHP 8.2* e *MariaDB*):
1. Copiare la cartella *`public_html`* nella corrispettiva cartella dove Apache andrà a prendere i file da servire;
2. Copiare la cartella *`src`* nella stessa directory della cartella *`public_html`* o equivalente, come mostrato in *@struttura*;
3. Eseguire lo script *`db.sql`* su *MariaDB*;
4. Per configurare l'accesso al database ci sono 2 modi:
    - Nel proprio ambiente dove vi è il server apache configurare le seguenti variabili d'ambiente;
    - Andare su `src/config.php` e modificare i valore nelle linee 2-5 rimpiazzando i *`getenv("DB_*")`* con le stringhe dei rispettivi valori.
  Tali variabili sono:
    - `DB_HOST`: indica l'indirizzo del server in cui si trova il server MariaDB, nel nostro caso `localhost`;
    - `DB_NAME`: indica il nome del database di MariaDB;
    - `DB_USER`: indica l'username dell'account per accedere a MariaDB;
    - `DB_PASS`: indica la password associata all'username specificato precedentemente.
5. In base alla configurazione del server Apache (come ad esempio il caso del laboratorio) se il sito viene servito su una path diversa da *`"/"`* bisogna andare su *`public_html/.htaccess`* e cambiare la seconda riga dove vi indica la path che prenderà in considerazione per gestire le regole per il robots.txt, errori 404 e 500 (nel nostro caso è *`RewriteBase /mvasquez`*).
