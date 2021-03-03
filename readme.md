# Uruchomienie

## Wymagania

- Linux (preferowany Ubuntu)
- Docker
- Docker Compose

## Instalacja

1. Wykonaj polecenia po kolei:

    - `bin/docker start`
    - `bin/docker hosts:install`
    - `bin/composer install`

2. Zaloguj się do bazy danych za pomocą danych:

    Adres: http://localhost:8000/ <br />
    Login: `root`<br />
    hasło: `root`
    
    Wejdź do bazy danych o nazwie `development`, i zaimportuj do niej dane z pliku [dump.sql](dump.sql).

## Adresy URL

| Service       | Access Address                                |
|---------------|-----------------------------------------------|
| Frontend      | http://tulia.loc/                             |
| Backend       | http://tulia.loc/administrator/               |
| Baza danych   | http://localhost:8000/                        |
| Mailhog       | http://localhost:8025/                        |
| Elasticsearch | http://localhost:9200/                        |
| Kibana        | http://localhost:5601/                        |
| Dokumentacja  | http://doc.tulia.loc/docs/pl/current/szablony |

## Dane do logowania

### Backend

Login: `admin`<br />
Hasło: `MyP4$$w0rdT04Dm!n`

### Baza danych (phpmyadmin)

Login: `root`<br />
Hasło: `root`

Port (lokalny): `3306`
Port (zewnętrzny): `33061`
