Erste Auffälligkeiten (CF-AUDIT)


| CF     | Beschreibung                                                                   | Priorität |
| ------ | ------------------------------------------------------------------------------ | --------- |
| CF-001 | Entwicklungs-/Phasenkommentare bereinigen                                      | P3        |
| CF-002 | `normalize()` im Validator konsequent verwenden                                | P3        |
| CF-003 | Atomare Schreibvorgänge in `SubmissionStorage` verifizieren und ggf. absichern | P2        |
| CF-004 | Projektdokumentation erweitern                                                 | P2        |
| CF-005 | `README.md` vervollständigen                                                   | P1        |



🔴 CF-001
macOS-Dateien
Im ZIP befinden sich
__MACOSX/
.DS_Store
Diese gehören nicht in das Repository.
Status:
🔴 entfernen


🔴 CF-002
Logverzeichnis
Momentan:
logs/

faild/
Sollte heißen:
logs/

failed/
Das entspricht auch unserer Spezifikation.


🟡 CF-003
Bootstrap
Ich sehe momentan
src/Bootstrap.php
Im Verlauf hatten wir zuletzt
src/Bootstrap/ApplicationFactory.php
beschlossen.
Das ist keine funktionale Kritik.
Hier muss ich jetzt den vorhandenen Code prüfen.
Es kann sein, dass dein Bootstrap.php bereits genau diese Aufgabe erfüllt.
Dann benennen wir nichts um.
Wir vermeiden unnötige Änderungen.


🟡 CF-004
Mail
Mir ist bereits beim Dateinamen aufgefallen:
MailMassage.php
Ich vermute stark,
gemeint war
MailMessage.php
Das muss ich prüfen.
Wenn tatsächlich überall
MailMassage
verwendet wird,
werden wir das sauber migrieren.