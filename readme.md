# Uruchomienie

## Wymagania

- Linux (preferowany Ubuntu)
- Docker
- Docker Compose

## Instalacja

1. Wykonaj polecenia po kolei:

    - `tulia/bin/env docker:start`
    - `tulia/bin/env composer:install`
    - `tulia/bin/env hosts:install`

2. Zaloguj się do bazy danych za pomocą danych:

    Adres: http://localhost:8000/ <br />
    Login: `root`<br />
    hasło: `root`
    
    Wejdź do bazy danych o nazwie `development`, i zaimportuj do niej dane z pliku [dump.sql](dump.sql).

## Adresy URL

| Service      | Link                                          |
|--------------|-----------------------------------------------|
| Frontend     | http://tulia.loc/                             |
| Backend      | http://tulia.loc/administrator/               |
| Baza danych  | http://localhost:8000/                        |
| Mailhog      | http://localhost:8025/                        |
| Dokumentacja | http://doc.tulia.loc/docs/pl/current/szablony |

## Dane do logowania

### Backend

Login: `admin`<br />
Hasło: `q1w2e3R$T%Y^`

### Baza danych

Login: `root`<br />
hasło: `root`
