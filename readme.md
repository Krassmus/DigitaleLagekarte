# DigitaleLagekarte Kat.IP

Ein Plugin für Stud.IP, um pro Veranstaltung bzw. Übung/Einsatz eine Lagekarte zu führen. Das Projekt wird von Rasmus Fuhse entwickelt für das Technische Hilfswerk Fachgruppe Führung und Kommunikation. Ziel ist es, einen transportablen Server mit Stud.IP im Einsatz zu haben, auf dem eine digitale Lagekarte des Schadensgebietes mithilfe eines offline OpenStreetMap-Mirrors geführt werden kann.

## Vorteile

* **Betriebsystemunanbhängigkeit und Netzwerkfähigkeit**: Beliebig viele Leute können auf ein und dieselbe Lagekarte schauen - ob über den Beamer, Großbildfernseher, dem eigenen Notebook, dem Tablet oder gar einem Smartphone ist dabei egal. Alle sehen immer in Echtzeit dieselbe Lagekarte und sind auf demselben Stand, und können trotzdem ihr Lieblingswerkzeug benutzen.
* **Opensource**: Mitglieder des Katastrophenschutzes schätzen es, wenn sie das Stromaggregat, das im Einsatzfall gerade ausgefallen ist, auch mal selbst reparieren können. Opensource-Software ist das Äquivalent zu einem leicht wartbaren Stromaggregat im Softwarebereich. Sie ist nicht nur kostenlos verfügbar, sondern auch leicht wartbar. Wenn mal ein Fehler auftritt, kann man selbst *unter die Haube* schauen, den Fehler beheben oder vielleicht sogar mal Zusatzwünsche einbauen, ohne von irgendeiner Softwarefirma abhängig zu sein. Alle Komponenten der digitalen Lagekarte vom Webserver über das Webportal Stud.IP bis hin zum Lagekartenplugin und dem Kartenmaterial stehen unter freien Lizenzen.
* **Einbindung externer Quellen**: *"Ruf mal alle viertel Stunde die Pegelstände ab und trage sie in die Karte ein."* Solche Aufgaben lassen sich automatisieren. Zu fast allen im Katastrophenfall wichtigen Daten gibt es offene Schnittstellen im Internet, die dazu passende JSON-Datenpakete anbieten. In der Lagekarte muss man nur die Webadresse eingeben und danach die Textfolge mit einem Marker auf der Karte verknüpfen und schon werden alle maximal zehn Minuten die Daten automatisch abgerufen und in die Lagekarte eingebaut. Ob das dann Pegelstände sind oder Wetterdaten oder Daten eines GPS-Trackers, ist dann egal. Die Schnittstelle ist absolut generisch gebaut. Funktioniert natürlich nur, wenn auch Internet vorhanden ist. Aber es entstehen weniger Flüchtigkeitsfehler und spart Zeit, sodass man im Einsatz mehr Manpower für die wichtigen Dinge hat.
* **Archivierung des gesamten Einsatzes**: *Wo ist denn die Einheit hin verschwunden?* *wie war die Stärkemeldung gestern um 12 Uhr?* - Da die digitale Lagekarte praktisch jederzeit gespeichert werden kann, entsteht ein komplettes Archiv des gesamten Einsatzes. Man kann sich genau anzeigen, wo eine Einheit gewesen ist, wie die Karte sich mit der Zeit verändert hat. Das alles kann automatisch passieren und jedes Backup im Archiv hat noch alle Informationen bis hin zum kleinen Kommentar unterhalb der Stärkemeldung einer kleinen Einheit im Untereinsatzabschnitt.
