# cyfrowauczelnia


Instrukcje Uruchomienia Projektu z Użyciem Docker
Ten projekt korzysta z Docker i docker-compose do uruchomienia środowiska deweloperskiego, które obejmuje aplikację PHP i bazę danych SQL Server.

1. Wymagania Wstępne
Zainstaluj Docker Desktop na swoim komputerze. Pobierz z oficjalnej strony Docker i postępuj zgodnie z instrukcjami instalacji.
Upewnij się, że Docker Desktop jest uruchomiony.
Zainstaluj Visual Studio Code (VS Code), jeśli jeszcze tego nie zrobiłeś, ze strony Visual Studio Code.

2. Konfiguracja Projektu
Sklonuj repozytorium projektu na swój lokalny komputer używając polecenia git clone https://github.com/areyomad/cyfrowauczelnia.git
Otwórz folder projektu w Visual Studio Code.
Upewnij się, że w głównym katalogu projektu znajdują się pliki Dockerfile i docker-compose.yml oraz katalogi aplikacji.

3. Uruchomienie Projektu
Aby uruchomić projekt, wykonaj następujące kroki:

Otwórz terminal w Visual Studio Code naciskając `Ctrl + `` lub przez menu Terminal > Nowy Terminal.

W terminalu przejdź do katalogu głównego Twojego projektu (jeśli już w nim nie jesteś).

Uruchom wszystkie usługi zdefiniowane w docker-compose.yml używając polecenia:

docker-compose up
To polecenie zbuduje obrazy Docker (jeśli potrzeba) i uruchomi kontenery.

Poczekaj, aż wszystkie usługi zostaną uruchomione. W terminalu powinny pojawić się logi informujące o uruchomieniu usług.

4. Dostęp do Aplikacji i Narzędzi
Twoja aplikacja PHP powinna być dostępna pod adresem http://localhost, chyba że zdefiniowano inny port w docker-compose.yml.
phpMyAdmin (jeśli skonfigurowany) powinien być dostępny pod adresem http://localhost:8001.
Połączenia z bazą danych można realizować używając danych konfiguracyjnych zdefiniowanych w docker-compose.yml.
Zatrzymanie i Usuwanie Kontenerów
Aby zatrzymać i usunąć kontenery, sieci, woluminy oraz obrazy utworzone przez docker-compose, użyj polecenia:

docker-compose down