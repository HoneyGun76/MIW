# Panduan Pembuatan Diagram UML untuk Arsitektur Jaringan MIW

## Panduan Diagram Deployment UML

Untuk meningkatkan diagram arsitektur jaringan MIW menggunakan standar UML, berikut adalah panduan yang dapat diikuti:

### Elemen yang Harus Ada dalam Diagram:

1. **Nodes (Perangkat)**:
   - Client (Pengguna) - sebagai node perangkat komputer/mobile
   - Internet - sebagai cloud atau network
   - Platform Railway - sebagai node server dengan stereotype «PaaS»
   - Web Server - sebagai node dengan stereotype «server»
   - Database Server - sebagai node dengan stereotype «database»

2. **Components (Komponen Software)**:
   - Aplikasi PHP MIW - sebagai komponen dengan stereotype «application»
   - Database MySQL - sebagai komponen dengan stereotype «database»

3. **Connections (Koneksi)**:
   - Antara Client dan Internet: protokol HTTPS
   - Antara Internet dan Platform Railway: protokol HTTPS
   - Antara Web Server dan Database Server: protokol SQL/TCP

4. **Artifacts (Artefak)**:
   - File PHP aplikasi - ditampilkan sebagai file dalam Web Server
   - File Database - ditampilkan sebagai file dalam Database Server

### Notasi Standar UML:

```
+----------------------------------+
|        «device»                  |
|        Desktop/Mobile            |
|                                  |
+----------------------------------+
              |
              | «HTTPS»
              v
+----------------------------------+
|        «network»                 |
|        Internet                  |
|                                  |
+----------------------------------+
              |
              | «HTTPS»
              v
+----------------------------------+
|        «PaaS»                    |
|      Platform Railway            |
|  +----------------------------+  |
|  |  «server»                  |  |
|  |  Web Server                |  |
|  |  [Aplikasi PHP MIW]        |  |
|  +----------------------------+  |
|               |                  |
|               | «SQL/TCP»        |
|               v                  |
|  +----------------------------+  |
|  |  «database»                |  |
|  |  Database MySQL            |  |
|  |                            |  |
|  +----------------------------+  |
+----------------------------------+
```

### Tools yang Direkomendasikan:

1. **Draw.io / diagrams.net**: Tool gratis berbasis web dengan banyak template UML
2. **Visual Paradigm**: Tool profesional untuk diagram UML
3. **Lucidchart**: Tool berbasis web dengan kemampuan kolaborasi
4. **PlantUML**: Tool berbasis teks untuk membuat diagram UML melalui kode

### Kode PlantUML untuk Arsitektur MIW:

```
@startuml
!define RECTANGLE class

skinparam node {
  BorderColor black
  BackgroundColor lightblue
  FontSize 14
}

skinparam database {
  BorderColor black
  BackgroundColor lightgrey
}

skinparam cloud {
  BorderColor black
  BackgroundColor white
}

node "Pengguna" as client {
  RECTANGLE "Browser" as browser
}

cloud "Internet" as internet

node "Platform Railway" as railway {
  node "Web Server" as webserver {
    artifact "Aplikasi PHP MIW" as app
  }
  database "Database MySQL" as db
}

client --> internet : HTTPS
internet --> railway : HTTPS
webserver --> db : SQL/TCP
@enduml
```

Dengan mengikuti panduan ini, diagram arsitektur jaringan MIW akan lebih profesional, standar, dan mudah dipahami oleh semua pihak yang terlibat dalam pengembangan maupun dokumentasi.
