
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
        *Indirizzo web:* URL DA METTERE
      ]
      #v(0.3cm)
      #text()[*Membri del gruppo di lavoro*]
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

#set heading(numbering: "1.1", supplement: "Paragrafo")

= Introduzione
ESU-Advisor è una piattaforma web dedicata agli studenti universitari di Padova che permette di consultare i menu delle mense universitarie dell'ESU (Ente per il Diritto allo Studio Universitario) e di lasciare recensioni sui piatti disponibili. Il progetto nasce dall'esigenza di fornire agli studenti uno strumento semplice e accessibile per valutare e condividere informazioni sui pasti offerti nelle diverse mense universitarie.

Il sito web permette agli utenti non registrati di visualizzare le informazioni sulle mense e sui relativi piatti del menu settimanale, mentre gli utenti registrati possono lasciare recensioni sui piatti, gestire le proprie preferenze alimentari e accedere a funzionalità avanzate di accessibilità.
= Analisi delle Utenze <analisiutenze>
In base a delle riflessioni sulle possibili utenze del sito, è stato dedotto che le caratteristiche degli utenti tipici che potrebbero utilizzare il sito web sono le seguenti:
- *Studenti universitari* presso l'Università di Padova
- Età compresa tra 18 e 30 anni
- Utilizzo frequente di dispositivi mobili
- Esigenze alimentari specifiche (allergie, intolleranze, preferenze)
- Budget limitato e ricerca di opzioni convenienti

Di conseguenza, ci si aspetta che gli utenti effettuino ricerche mirate per informazioni sulle mense universitarie, come ad esempio:
- "mense universitarie Padova"
- "menu mensa ESU oggi"
- "recensioni cibo mense Padova"
- "orari mense università Padova"
- "piatti del giorno mense universitarie"

Ci si aspetta inoltre che gli utenti accedano alla piattaforma principalmente tramite dispositivi mobili, in quanto si prevede queste ricerche vengano effettuate mentre si è in movimento e ad un certo punto della giornata, in questo caso l'ora di pranzo.

= SEO
da milgiorare
- *Meta tags* appropriati per ogni pagina
- *Sitemap XML* generata automaticamente
- *URLs SEO-friendly* con mod_rewrite
- *Ottimizzazione delle immagini* con tag alt descrittivi
- *Performance*


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
In base a quanto dedotto nella sezione di analisi delle utenze (#text(style: "italic")[@analisiutenze]), è stato deciso adottare un *approccio mobile-first* nella progettazione dell'interfaccia utente.
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
== Architettura MVC
Il progetto è stato strutturato secondo il pattern architetturale Model-View-Controller (MVC) per garantire una chiara separazione tra la presentazione e il comportamento del sito web:

=== Models
I modelli gestiscono l'accesso ai dati e l'interazione con il database:
- `UserModel`: Gestione degli utenti, autenticazione e profili
- `MenseModel`: Gestione delle informazioni sulle mense
- `PiattoModel`: Gestione dei piatti e delle loro proprietà
- `RecensioneModel`: Gestione delle recensioni degli utenti
- `PreferenzeUtenteModel`: Gestione delle preferenze di accessibilità

=== Views
Le viste si occupano della presentazione dei dati all'utente:
- `BaseView`: Classe astratta che implementa funzionalità comuni a tutte le viste
- Viste specifiche per ogni pagina (`IndexView`, `PiattoView`, `ProfileView`, ecc.)
- Generazione dinamica del contenuto HTML basata sui dati forniti dai controller

=== Controllers
I controller gestiscono il flusso dell'applicazione e coordinano modelli e viste:
- `BaseController`: Interfaccia che definisce i metodi comuni a tutti i controller
- Controller specifici per ogni funzionalità (`IndexController`, `LoginController`, `ReviewController`, ecc.)
- Gestione delle richieste HTTP (GET/POST) con metodi dedicati

== Database
Il database è stato progettato per gestire efficacemente le relazioni tra entità:

- *Utenti*: Informazioni sugli account e preferenze
- *Mense*: Dettagli sulle mense disponibili
- *Piatti*: Catalogo dei piatti con descrizioni e categorie
- *Menu*: Associazione tra mense e piatti disponibili
- *Recensioni*: Valutazioni degli utenti sui piatti
- *Allergie*: Gestione delle informazioni sugli allergeni

=== Generazione Menu Settimanale
da migliorare
Stored procedure per varietà automatica:
- 3 primi piatti casuali per mensa
- 3 secondi piatti casuali per mensa
- 2 contorni casuali + insalata fissa
- Rotazione settimanale automatica

== Sistema di routing
da migliorare
Il routing delle richieste è gestito attraverso script PHP dedicati:

- Ogni pagina dell'applicazione corrisponde a un file PHP nella directory `public_html`
- I file gestiscono il dispatching delle richieste al controller appropriato
- Gestione differenziata di richieste GET e POST
- Reindirizzamenti automatici in caso di accesso non autorizzato

== Gestione degli errori
Se l'utente visita un link errato o inesistente, ad esempio l'url di un film rimosso, viene mostrata una
pagina 404 personalizzata. Allo stesso modo, per errori lato server (collegamento assente a DB ecc.)
viene mostrata una pagina di errore 500. I messaggi di errore sono informativi e offrono all'utente una
soluzione al problema

= Test effettuati e metodologie di testing
== Validazione Codice
== Test di Accessibilità

= Suddivisione del Lavoro
La suddivisione del lavoro è stata effettuata considerando le competenze individuali e la necessità di garantire
una copertura completa di tutti gli aspetti del progetto.

Viene riportata di seguito la suddivisione delle attività principali:

== Dettaglio attività svolte
*Giacomo Loat*
- Progettazione e implementazione dell'architettura MVC
- Creazione del sistema di routing e gestione delle richieste
- Sviluppo del sistema di autenticazione (login/registrazione)
- Implementazione della gestione profilo utente
- Creazione della struttura di base delle pagine e dei template
- Gestione errori e pagine 404/500

*Giulio Bottacin*
- Sviluppo del sistema di recensioni e valutazioni
- Implementazione della visualizzazione dei piatti del giorno
- Creazione delle interfacce per la visualizzazione delle mense
- Gestione delle recensioni degli utenti
- Progettazione e implementazione delle card dei piatti
- Ottimizzazione delle query per il caricamento dei contenuti

*Malik Giafar Mohamed*
- Progettazione e implementazione del database
- Creazione delle stored procedure per la generazione menu settimanale
- Sviluppo del sistema di gestione mense e piatti
- Implementazione del sistema di filtraggio per allergeni
- Ottimizzazione delle performance del database
- Documentazione tecnica e relazione del progetto

*Manuel Felipe Vasquez*
- Implementazione delle funzionalità di accessibilità
- Sviluppo della modalità scura e personalizzazione testo
- Integrazione del font OpenDyslexic per utenti con dislessia
- Realizzazione del design responsive mobile-first
- Test di usabilità e accessibilità
- Ottimizzazione SEO e miglioramento meta tag
