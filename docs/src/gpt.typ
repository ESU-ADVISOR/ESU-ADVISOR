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
      #text(size: 15pt)[
        *Indirizzo web:* http://tecweb.studenti.math.unipd.it/~malik/ESU-ADVISOR/
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
      #v(1cm)
      #text(size: 12pt)[
        *Credenziali utente:* user / user \
        *Email referente:* malik.giafarmohamed\@studenti.unipd.it
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
ESU-Advisor è una piattaforma web dedicata agli studenti universitari di Padova che permette di consultare i menu delle mense universitarie dell'ESU (Ente per il Diritto allo Studio Universitario) e di lasciare recensioni sui piatti disponibili. Il progetto nasce dall'esigenza di fornire agli studenti uno strumento semplice e accessibile per valutare e condividere informazioni sui pasti offerti nelle diverse mense universitarie.

== Composizione del Team
Il progetto è stato realizzato da un gruppo di quattro studenti con competenze complementari:

#figure(
  caption: [Membri del team e relativi ruoli],
  table(
    columns: 4,
    [*Nome*], [*Cognome*], [*Matricola*], [*Competenze Principali*],
    [Giacomo], [Loat], [2077677], [HTML5, Design UI/UX, Struttura Frontend],
    [Giulio], [Botta], [2042340], [CSS3, Responsive Design, Accessibilità],
    [Malik], [Giafar Mohamed], [2075543], [Database Design, SQL, Architettura Dati],
    [Manuel Felipe], [Vasquez], [2076425], [PHP, Backend Development, API Design]
  )
)

== Analisi degli Utenti Target
=== Caratteristiche Primarie degli Utenti
Il pubblico di riferimento è costituito principalmente da studenti universitari di Padova, con un'età compresa tra 18 e 30 anni, che fanno un uso frequente di dispositivi mobili.  Si è tenuto conto anche delle loro possibili esigenze alimentari specifiche (come allergie o intolleranze) e della loro attenzione a opzioni di pasto convenienti, dato il budget limitato.

=== Possibili Ricerche sui Motori di Ricerca
- "mense universitarie Padova"
- "menu mensa ESU oggi"
- "recensioni cibo mense Padova"
- "orari mense università Padova"
- "piatti del giorno mense universitarie"

== Suddivisione del Lavoro

=== Componente di Contenuto (Giacomo Loat)
- Sviluppo della struttura HTML5
- Implementazione dei template delle pagine
- Integrazione dei contenuti dinamici
- Ottimizzazione per SEO

=== Componente di Presentazione (Giulio Bottacin)
- Implementazione dello stile CSS
- Creazione delle componenti UI riutilizzabili
- Implementazione del design responsive
- Sviluppo delle feature di accessibilità (WCAG 2.0)
- Gestione dei temi chiaro/scuro
- Supporto per utenti con dislessia

=== Componente di Comportamento (Manuel Felipe Vasquez)
- Sviluppo della logica JavaScript client-side
- Implementazione delle validazioni form
- Architettura MVC backend in PHP
- Gestione dell'autenticazione e autorizzazione
- Sviluppo delle API REST

=== Struttura Database (Malik Giafar Mohamed)
- Design dello schema del database normalizzato
- Implementazione delle relazioni tra entità
- Ottimizzazione delle query
- Gestione delle procedure stored e trigger
- Implementazione del sistema di gestione allergeni

== Metodologia di Collaborazione

=== Strumenti e Workflow
- *Git* per il controllo versione distribuito
- *GitHub* per la gestione del repository e code review
- *Docker* per lo sviluppo containerizzato senza prerequisiti di sistema
- *Visual Studio Code* come IDE condiviso con Live Share

= Progettazione

== Schema organizzativo
Siccome la struttura del sito web si differenzia nel contenuto solo nel tag `<main>` mantentendo la struttura,
composta da header e sidebar, pressochè invariata. Si può dire che lo schema organizzativo ambiguio scelto per
il raggruppamento dei link alle pagine principali si applica a tutto il sito web.

Nel sito web infatti, sarà sempre presente una sidebar a sinistra, che contiene i link alle pagine principali del sito, tra le quali è possibile trovare:
- *Home*: Pagina principale con i piatti del giorno e le recensioni
- *Review*: Pagina per la visualizzazione e l'inserimento di recensioni sui piatti
- *Profilo*: Pagina del profilo utente per visualizzare le informazioni personali e le recensioni.
- *Impostazioni*: Pagina per la gestione di preferenze alimentari, stilistiche del sito, dello username e per la cancellazione dell'account.

== Schema a tre pannelli
Al fine di ridurre il fenomeno del disorientamento dell'utente, è stato scelto di adottare lo schema a tre pannelli, adattando il design del sito per risponere a quese tre domande:
- *Dove sono?*
  - L'utente può orientarsi grazie all'evidenziazione della pagina corrente nella sidebar,  oppure tramite la breadcrumb presente nell'header
- *Dove posso andare?*
  - L'utente può navigare tra le pagine del sito tramite la sidebar, che mostra le sezioni principali del sito web. \ Sono inoltre presenti dei link all'interno del contenuto del sito che portano alla visualizzazione dei piatti del giorno, rispondendo alla domanda *_Cosa posso fare?_*.
- *Di cosa si tratta?*
 - Ogni azione intraprendibile è descritta in modo chiaro e conciso, con l'eventuale uso di icone e testi esplicativi per facilitare la comprensione. Alcuni esempi sono i link "vedi le recensioni" presenti nelle schede dei piatti della homepage.

Il sito inoltre risponde alla domanda *Dove posso trovare altre informazioni?* mettendole a disposizione nella homepage o esplicitandole in dei link.

== Convenzioni interne
Per garantire uno sviluppo coerente e una manutenzione efficace del progetto, sono state stabilite le seguenti convenzioni:
- *breadcrumb*
- *stile bottoni*
- *stile link*
- *card con lo stesso stile*

== Layout
In base a quanto dedotto nella sezione di analisi delle utenze, è stato deciso adottare un *approccio mobile-first* nella progettazione dell'interfaccia utente.
Questo non significa soltanto aver adottato un *layout responsive*, ma significa che il design e lo sviluppo dell'applicazione sono stati inizialmente focalizzati sulle esigenze degli utenti che utilizzano dispositivi mobili, siccome questi saranno coloro che utilizzeranno maggiormente il sito web.

== Funzionalità
Il sito web offre le seguenti funzionalità principali:
=== Autenticazione
Gli utenti possono registrarsi e accedere al sito per usufruire di funzionalità avanzate, come la possibilità di lasciare recensioni sui piatti e personalizzare le proprie preferenze alimentari.
=== Visualizzazione piatti del giorno
Gli utenti possono visualizzare i piatti del giorno delle mense universitarie, con dettagli su ingredienti, allergeni e recensioni degli utenti.
=== Recensioni
Gli utenti registrati possono lasciare recensioni sui piatti, valutandoli e fornendo commenti utili ad altri studenti.
=== Preferenze alimentari
Gli utenti possono impostare preferenze alimentari specifiche, come allergie o intolleranze, per ricevere avvisi sui piatti che potrebbero contenere ingredienti indesiderati.
=== Personalizzazione impostazioni
Nella pagina delle impostazioni, gli utenti sia autenticati che non possono personalizzare la propria esperienza di navigazione, in particolare, è possibile:
- Attivare la *modalità scura* per ridurre l'affaticamento visivo
- Modificare le *dimensioni del testo* per una lettura più confortevole
- Cambiare il font in uno specifco font per la *dislessia* per migliorare la leggibilità del testo

Il font per la dislessia scelto è stato "OpenDyslexic", un font open source progettato specificamente per facilitare la lettura dei contenuti testuali presenti nel sito.

Inoltre, sempre nella stessa pagina è possibile impostare una mensa preferita, che verrà mostrata subito al login senza necessità di selezionarla ogni volta.
Sarà anche possibile impostare delle preferenze alimentari in modo che possano essere segnalati i piatti che contengono un insieme di allergeni specificati dagli utenti.


= Implementazione

== Struttura delle Directory
l progetto è organizzato con una netta separazione tra i file accessibili pubblicamente (public-html), che includono pagine PHP, stili, script e risorse, e il codice sorgente del backend (src), che contiene i file MVC e di configurazione, non esposti direttamente sul web.

```bash
ESU-ADVISOR/
├── docs/                    # Documentazione progetto
│
├── public-html/             # File esposti pubblicamente accessibili
│   ├── styles/             # Fogli di stile modulari
│   ├── scripts/            # Script client-side
│   ├── images/             # Risorse grafiche
│   ├── fonts/              # Cartella per i font
│   ├── *.php               # Pagine dell'applicazione
│   ├── .htaccess           # File per la risoluzione delle path del server APACHE
│   ├── robots.txt          # File per indicare ai crawler le pagine che possono visitare
│   └── index.php           # Entry point applicazione
│
├── src/                     # File utilizzati lato backend non ottenibili dal webserver
│   ├── controllers/        # Controller MVC
│   ├── models/             # Model MVC
│   ├── templates/          # File HTML puri contententi la struttura della pagina
│   ├── views/              # View MVC
│   ├── config.php          # File per le variabili d'ambiente per l'accesso al DB
│   └── session_init.php    # File per le impostazioni della sessione di PHP
│
├── db.sql                   # Schema database e query di inserimento dati iniziali
└── README.md
```

== Backend PHP

=== Pattern Architetturale MVC
Il progetto implementa rigorosamente il pattern Model-View-Controller (MVC) per garantire modularità e manutenibilità, dove:

==== Model Layer
Gestisce la logica dei dati e le interazioni con il database:
- *DatabaseModel*: Gestione dell'accesso al database
- *MenseModel*: Informazioni sulle mense e orari
- *MenuModel*: Informazioni sui menu delle mense e i loro piatti
- *PiattoModel*: Catalogo piatti con categorie e allergeni
- *PreferenzeUtenteModel*: Personalizzazione esperienza utente
- *RecensioneModel*: Sistema di valutazioni e commenti
- *UserModel*: Gestione utenti e autenticazione

==== View Layer
Si occupa della presentazione dei dati attraverso un sistema di template che separa il contenuto dalla formattazione:
- *BaseView*: Classe di base da cui ogni view eredita le funzionalità
- *ErrorView*: Gestione della pagina di errore
- *IndexView*: Homepage con selezione mense
- *LoginView* / *RegisterView*: Autenticazione
- *PiattoView*: Dettaglio piatto e recensioni
- *ProfileView*: Dettaglio profilo
- *ReviewView*: Dettaglio di una recensione
- *ReviewEditView*: Dettaglio piatto e recensioni
- *SettingsView*: Gestione preferenze e accessibilità

==== Controller Layer
Gestisce le richieste dell'utente, dialoga con il Model e seleziona la View appropriata da mostrare con i dati appropriati:
- *BaseController*: Classe di base da cui ogni controller eredita le funzionalità
- *ErrorController*: Gestisce la richiesta errata
- *IndexController*: Logica homepage e selezione mense
- *LoginController* / *RegisterController*: Autenticazione
- *PiattoController*: Gestione visualizzazione piatti
- *ProfileController*: Gestisce le informazioni dell'utente
- *ReviewController*: Gestione recensioni
- *ReviewEditController*: Gestione per le modifiche alle recensioni
- *SettingsController*: Configurazione utente

=== Tecnica di Templating
Per separare la struttura dal comportamento abbiamo fatto uso del tag *`<template>`* fornito da HTML per evitare di inserire snippet di PHP all'interno della struttura del documento. Per ogni tag viene aggiunto un id univoco con il formato *`nome-contenuto-template`*. Poi per inserire i dati relativi si è fatto uso della funzione *`replaceTemplateContent($dom, $templateId, $newContent)`* presente nel file `Utils.php`, dove:
- *`$dom`* contiene il testo dell'intero file html parsabile contenente i tag *`<template>`*
- *`$templateId`* l'id del tag *`<template>`* che si vuole sostituire
- *`$newContent`* il contenuto in formato HTML da inserire

Per effettuare il parsing del file e ottenere la posizione del tag è stata utilizzata la classe *`DOMDocument`* di PHP, che fornisce una serie di funzioni per gestire e manipolare i file HTML. Nel nostro caso specifico utilizziamo la classe per controllare che il contenuto inserito sia formattato correttamente con caratteri utf-8 e che rispetti la sintassi HTML.

Questa funzione viene utilizzata spesso per gestire tutte le varie componenti del sito che richiedono dati provienienti dal DB o che richiedono di essere processate, ad esempio viene utilizzata per modularizzare la struttura della pagina, in particolare ogni View eredita dalla classe *`BaseView`* che espone il metodo *`render()`* che di default inserisce le componenti *`header`*, *`footer`* e *`sidebar`* in ogni file html che viene richiesto affinche esistano i tag template corrispettivi. Questo permette di separare la struttura di componenti ripetute in file unici favorendo la modularita del sito e permettendo a più componenti del gruppo di lavorare sul progetto evitando il più possibile di avere conflitti nelle modifiche.

== Stili CSS
Per gestire gli stili che abbiamo applicato alla pagina sono state definite delle variabili globali per rendere coerenti le proprieta dei vari elementi, come colore dei bottoni, il testo, background di elementi ripetuti, spaziature, font, etc..., inoltre vi è presente la variazione delle stesse variabili per il tema scuro che verranno applicate in base ad una classe che verrà aggiunta al nodo root *`<html>`* per favorire l'uso delle stesse classi per entrami i temi

== Scripts Javascript e Validazione dell'input
Sono stati utilizzati script javascript per gestire il comportamento del sito, in particolare:
- *Validazione dell'input dei form lato client*: non strettamente necessari in quanto vi è l'equivalente validazione a lato server per motivi di sicurezza, ma aiuta l'utente ad inserire correttamente i dati richiesti prima di ritrovarsi con una pagina d'errore
- *Funzionalità di ricerca dei piatti nel menù*: per favorire la ricerca di alimenti o allergeni tra i piatti della mensa attualmente selezionata in maniera veloce, vi è implementata una barra di ricerca in cima alla sezione del menu del giorno, che filtrerà i piatti presenti nascondendo quelli che non corrispondono ai termini di ricerca.
- *Suggerimenti piatti durante la recensione*: per facilitare il processo di recensione dei piatti, una volta selezionata la mensa, al momento della scrittura del nome del piatto, verrà fornita una lista dei piatti di cui la mensa dispone al fine di evitare problemi di spelling del nome del piatto. Questa scelta è preferibile rispetto ad avere soltanto una lista, come nel caso della selezione della mensa, per rendere più semplice e veloce l'inserimento del nome del piatto nel caso il menù della mensa contenesse numerevoli opzioni.

Il sito web inoltre può funzionare correttamente pur non avendo abilitato javascript nel browser. In tal caso verrà mostrato un avviso che intimerà all'utente di abilitarlo.

== Gestione degli errori di navigazione
Nel caso si volesse fare accesso a link che puntano a risorse inesistenti or malformati, come ad esempio `http://<server>/index.php/wrong/path` o `http://<server>/wrong-filename.php`, sono state definite delle regole nel file `public_html/.htaccess` per indicare cosa fare in queste condizioni. In particolare se un utente volesse visitare una pagina che non esite o che non può visitare senza un account (ad esempio `review.php` e `profile.php`), essi verrebbero reindirizzati alla pagina `error.php` dove in base alle condizioni del reindirizzamento verranno indicate informazioni utili all'utente per comprendere cosa è appena successo e ad indicargli cosa dovrebbe fare, nel esempio corrente gli indicherà che "È necessario effettuare l'accesso per visualizzare questa pagina", con sotto i pulsanti per effettuare l'accesso o la registrazione se l'utente non ha già un account.

Queste regole sono state scritte per supportare deployment del sito web sia nel caso in cui sia presente nel root del server (`http://<server>/index.php`) o che si trovi in una sottocartella, come nel caso del server universitario (`http://<server>/<name>/index.php`)

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
Lo schema si articola attorno a tre entità fondamentali: le *mense*, i *piatti* e gli *utenti*.
- *mensa:* Questa tabella contiene le informazioni anagrafiche di ogni mensa universitaria. Il nome della mensa funge da chiave primaria e identificatore univoco. Gli altri campi includono l' indirizzo, il numero di telefono e un link esterno a Google Maps per la localizzazione geografica.
- *piatto:* Memorizza il catalogo di tutti i piatti che possono essere serviti. Ogni piatto è identificato univocamente dal suo nome (chiave primaria) e appartiene a una categoria predefinita ("Primo", "Secondo", "Contorno"). Contiene inoltre una descrizione testuale.
- *utente:* Gestisce i dati degli utenti registrati alla piattaforma. Ogni utente ha un id numerico auto-incrementale come chiave primaria e uno username univoco. La tabella memorizza anche la password (che verrà sottoposta ad hashing prima dell'inserimento) e la data di nascita.

Le entità principali sono collegate tra loro e arricchite di dettagli attraverso una serie di tabelle satellite.
- *orarioapertura:* Definisce gli orari di apertura settimanali per ciascuna mensa, creando una relazione uno-a-molti con la tabella mensa. Un vincolo di CHECK assicura che il `giornoSettimana` sia un valore compreso tra 1 e 7.
- *menu:* Si tratta di una tabella di collegamento che implementa una relazione molti-a-molti tra piatto e mensa, specificando quali piatti sono disponibili in quali mense.
- *recensione:* È la tabella centrale per le interazioni degli utenti. Stabilisce una relazione tra un utente, un piatto e una mensa. Un utente può lasciare una sola recensione per un dato piatto in una specifica mensa. La recensione include un voto (da 1 a 5), una descrizione testuale opzionale e la data di inserimento. La chiave esterna composta (piatto, mensa) garantisce che una recensione possa essere lasciata solo per un piatto effettivamente presente nel menu di quella mensa.


Infine, sono presenti tabelle per gestire informazioni aggiuntive sui piatti e le preferenze degli utenti.
  - *piatto_foto* e *piatto_allergeni:* Queste due tabelle aggiungono dettagli ai piatti. La prima gestisce l'associazione uno-a-molti tra un piatto e le sue foto (salvando il percorso all'immagine nel db). La seconda definisce una relazione molti-a-molti per associare a ogni piatto uno o più allergene da una lista predefinita conforme alla normativa europea.
  - *preferenze_utente* e *allergeni_utente:* Queste tabelle sono dedicate alla personalizzazione dell'esperienza utente. `preferenze_utente` ha una relazione uno-a-uno con utente e memorizza impostazioni di accessibilità come la `dimensione_testo`, l'uso del font per la dislessia (`modifica_font`), il `modifica_tema` visivo e la `mensa_preferita`. `allergeni_utente`, invece, permette agli utenti di registrare i propri allergeni personali in una relazione molti-a-molti, per ricevere avvisi mirati.

== SEO e Performance
Al fine di ottimizzare il posizionamento del sito nei motori di ricerca, sono state implementate le seguenti strategie:
- *Definizione di Meta tags*: sono stati creati meta tag specifici per ogni pagina, inclusi titolo e descrizione per ogni pagina del sito web. 
- *Sitemap XML*: è stata creata una sitemap per facilitare l'indicizzazione da parte dei motori di ricerca, che include tutte le pagine principali del sito. La sitemap viene generata automanticamente dallo script `sitemap.php` presente nella cartella `public_html/`, e viene aggiornata includendo ogni pagina del sito web, al momento dell'accesso.
- *URLs SEO-friendly*: Ogni URL del sito web è stato progettato per essere descrittivo e contenere parole chiave pertinenti, evitando di utilizzare parametri complessi o identificatori numerici. Ad esempio, l'URL per la pagina di recensioni di un piatto specifico è strutturato come `http://<server>/review.php?piatto=<nome-piatto>&mensa=<nome-mensa>`, dove `<nome-piatto>` e `<nome-mensa>` sono i nomi dei piatti e delle mense rispettivamente.

= Accessibilità

== Personalizzazione Accessibilità
Le impostazioni di accessibilità possono essere gestite sia dagli utenti registrati che non. Esse verranno salvate all'interno della sessione di PHP, e nel caso tali fossero salvate per un utente registrato, una volta effettuato l'accesso esse verranno applicate automaticamente. Le impostazioni disponibili sono: 

- *Dimensioni testo*: Piccolo, medio, grande
- *Font per dislessia*: OpenDyslexic font selezionabile dalle impostazioni
- *Temi*: Chiaro, scuro, sistema (prende l'impostazione dal tema del browser), tale viene caricato prima che l'utente possa visualizzare la pagina per evitare flashing

== Navigazione accessibile
Vengono riportati qui sotto altri aspetti che rendono questo sito accessibile a tutte le tipologie di utenti:

- *Navigazione da Tastiera*: Il sito e interamente utilizzabile tramite la tastiera e vengono nascosti gli elementi non interagibili della pagina (come la mensa e il nome del piatto durante la modifica di una recensione), e sono presenti dei link nascosti per navigare più velocemente la pagina ("Vai al contenuto" e "Vai alla navigazione")
- *Supporto per gli screen reader*: sono state utilizzate classi CSS per migliorare l'accessibilità del sito per gli utenti che necessitano l'uso di uno screen reader, dove tali applicano descrizioni e informazioni utili soprattuto riguardando le immagini dei piatti presenti nel sito e le stelle di valutazione nella pagina per la review, dove ad ogni stella vi è indicata il numero e il significato della valutazione.
- *Tabelle accessibili*: Ogni tabella del sito web è stata resa accessibile tramite: 
  - *attributi di scoping*: Ogni tabella ha un'intestazione chiara e descrittiva per ogni colonna, che aiuta gli utenti a comprendere il contenuto della tabella.
  - *tag di accessibilità*: Lo scopo principale delle tabelle nel sito è quello di mostrare gli orari di apertura delle mense di padova, quindi sono stati utilizzati i tag `<abbr>` per abbreviare i nomi dei giorni della settimana, ad esempio "Lun" per "Lunedì", "Mar" per "Martedì", etc. Questo aiuta gli utenti a comprendere rapidamente il significato delle abbreviazioni. Inoltre, è stato utilizzato il tag `<time>` per indicare gli orari di apertura e chiusura delle mense, in modo che gli screen reader possano leggere correttamente le informazioni temporali.
  - *descrizione accessibile e caption*: Ogni tabella ha una caption che descrive il suo contenuto e scopo, migliorando l'accessibilità per gli utenti di screen reader. È presente inoltre l'attirbuto `aria-describedby` per fornire una descrizione aggiuntiva della tabella, che viene letta dagli screen reader per fornire ulteriori informazioni sul contenuto della tabella.
- *Assenza di Link Circolari*: Non sono presenti link circolari o che portano a pagine senza contenuto utile, per evitare confusione e disorientamento dell'utente.
- *Form accessibili*: I form di inserimento dati sono stati progettati per essere accessibili, con etichette chiare e descrittive per ogni campo. Sono stati utilizzati attributi ARIA per migliorare l'accessibilità dei campi di input e dei pulsanti di invio.
- *Utilizzo di breadcrumbs*: È stata implementata una breadcrumb che mostra il percorso di navigazione dell'utente all'interno del sito, facilitando l'orientamento e la comprensione della struttura delle informazioni.

= Testing e Validazione
Il sito durante la fase di sviluppo e collaudo finale è stato sottoposto a numerevoli test che ci hanno permesso di evalutare alcune decisione prese e trovare parti che necessitavano correzioni o migliorie.

== Test di Accessibilità
=== Strumenti Utilizzati
- *WAVE* (Web Accessibility Evaluation Tool)
- *axe-core* accessibility checker
- *Screen reader testing* con NVDA
- *Keyboard navigation testing* completo
- *Total Validator* per individuare possibili problemi su ogni pagina del sito.


== Browser Testing
Testing completato su:
- Chrome 120+ (desktop/mobile)
- Firefox 121+ (desktop/mobile)
- Safari 17+ (desktop/mobile)
- Edge 120+ (desktop)

== Validazione Codice
=== HTML Validation
- *W3C Markup Validator*: 0 errori, 0 warning
- *HTML5 semantic validation*: Markup semanticamente corretto
- *XML compliance*: Sintassi XML valida

=== CSS Validation
- *W3C CSS Validator*: CSS3 valido
- *Cross-browser compatibility*: Chrome, Firefox, Safari, Edge
- *Mobile compatibility*: Android, iOS

=== PHP Code Quality
- *PSR-12* coding standards compliance
- *Static analysis* con PHPStan
- *Security scan* con RIPS
- *Performance profiling* con Xdebug

