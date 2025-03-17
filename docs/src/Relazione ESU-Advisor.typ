
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
      #v(0.5cm)
      #v(0.5cm)
      #text()[*A cura di*]
      #align(center)[
        #table(
          columns: 2,
          stroke: none,
          align(left)[*Membro*], align(left)[*Matricola*],
          align(left)[Giacomo Loat], align(left)[2077677],
          align(left)[Giulio Botta], align(left)[2042340],
          align(left)[Malik Giafar Mohamed], align(left)[2075543],
          align(left)[Manuel Felipe Vasquez], align(left)[2076425]
        )
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
== Composizione del Team
Il progetto è stato realizzato da un gruppo di quattro studenti con diverse competenze e background. La
tabella seguente riassume i membri del team e le loro principali competenze:
#figure(
  caption: [Membri del team e relativi ruoli],
  table(
    columns: 4,
    [*Nome*], [*Cognome*], [*Matricola*], [*Competenze*],
    [Giacomo], [Loat], [2077677], [HTML5, Design],
    [Giulio], [Botta], [2042340], [CSS3, UI/UX],
    [Malik], [Giafar Mohamed], [2075543], [SQL, Database Design, Data Modeling],
    [Manuel], [Felipe Vasquez], [2076425], [PHP, Backend Development, API Design]
  )
)

== Suddivisione del Lavoro
La suddivisione del lavoro è stata effettuata considerando le competenze individuali e la necessità di garantire
una copertura completa di tutti gli aspetti del progetto:

=== Componente di Contenuto
- Sviluppo della struttura HTML

=== Componente di Presentazione
- Implementazione del sistema CSS modulare
- Creazione delle componenti UI riutilizzabili
- Ottimizzazione per dispositivi mobili
- Implementazione delle feature di accessibilità

=== Componente di Comportamento
- Sviluppo della logica JavaScript client-side
- Implementazione delle validazioni form
- Implementazione dell’architettura MVC
- Gestione dell’autenticazione e autorizzazione

=== Struttura Database
- Design dello schema del database
- Ottimizzazione delle query

== Metodologia di Collaborazione
=== Strumenti e Workflow
Per facilitare la collaborazione, abbiamo utilizzato:
- Git per il controllo versione
- GitHub per la gestione del repository
- Docker per lo sviluppo decentralizzato del sito senza prerequsiti di sistema

=== Integrazione dei Componenti
L’integrazione tra i vari componenti è stata gestita attraverso:
- Sviluppo asincrono di componenti mutualmente esclusive
- L’utilizzo dei tag <template> per integrare i risultati del business layer indipendentemente dalla
componente di struttura

=== Gestione dei Conflitti
Per minimizzare e gestire i conflitti abbiamo:
- Definito convenzioni di codice chiare
- Implementato review obbligatorie pre-merge
- Mantenuto una comunicazione costante
- Utilizzato feature flags per sviluppi paralleli
- Organizzato daily standup meetings

=== Retrospettiva
La suddivisione del lavoro basata sulle competenze individuali ha permesso di:
- Massimizzare l’efficienza del team
- Garantire alta qualità in ogni componente
- Facilitare il processo di sviluppo parallelo
- Mantenere una base di codice pulita e ben organizzata
- Rispettare le tempistiche del progetto

= Descrizione Sito
ESUAdvisor è un’applicazione web progettata per consentire agli studenti universitari di consultare i menu
delle mense universitarie e lasciare recensioni sui pasti. Il progetto è stato sviluppato seguendo i principi di
accessibilità seguendo gli standard WCAG 2.0, manutenibilità e riutilizzabilità del codice.

= Architettura del Software
== Pattern MVC
Il progetto implementa il pattern Model-View-Controller (MVC) per separare chiaramente la logica di business (Models), la presentazione (Views) e il controllo del flusso applicativo (Controllers). Questa separazione
facilita:
- La manutenzione del codice
- Il testing delle singole componenti
- La scalabilità dell’applicazione
- Il riutilizzo delle componenti

== Struttura delle Directory
La struttura del progetto è organizzata in modo modulare:

