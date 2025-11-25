# Biblioteka Szkolna - System Zarządzania Biblioteką

## Opis Projektu

Projekt **Biblioteka Szkolna** to kompleksowa aplikacja webowa do zarządzania biblioteką szkolną. System został opracowany dla szkoły **Wesoła Szkoła** w Gdańsku i umożliwia zaawansowane zarządzanie księgozbiorem, wypożyczeniami, oraz komunikacją z użytkownikami.

## Technologie

- **PHP 7.4+** z programowaniem obiektowym (OOP)
- **MySQL/MariaDB** - baza danych
- **MySQLi** - obsługa bazy danych
- **HTML5** - struktura stron
- **CSS3** - stylizacja i animacje
- **JavaScript (vanilla)** - dynamiczne funkcjonalności
- **Responsive Web Design** - dostosowanie do urządzeń mobilnych

## Struktura Projektu

```
biblioteka_szkolna_v2/
├── admin/                    # Panel administracyjny
│   ├── pages/               # Podstrony panelu admin
│   ├── login.php            # Logowanie do panelu
│   ├── index.php            # Główny panel admina
│   └── logout.php           # Wylogowanie
├── website/                 # Strony publiczne
│   ├── home.php            # Strona główna
│   ├── search.php          # Wyszukiwanie książek
│   ├── guestbook.php       # Księga gości
│   ├── contact.php         # Formularz kontaktu
│   └── article.php         # Pełny artykuł
├── database/               # Warstwa dostępu do bazy
│   ├── config.php          # Konfiguracja i klasa Database
│   ├── Ksiazka.php         # Model Ksiazka
│   ├── Uczen.php           # Model Uczen
│   ├── Wypozyczenie.php    # Model Wypozyczenie
│   ├── News.php            # Model News
│   ├── Baner.php           # Model Baner
│   ├── KsiegaGosci.php     # Model Ksiega Gosci
│   └── schema.sql          # Schemat bazy danych
├── static/
│   ├── css/
│   │   ├── style.css       # Style dla strony publicznej
│   │   └── admin.css       # Style dla panelu admina
│   └── js/
│       └── main.js         # Skrypty JavaScript
├── images/                 # Zdjęcia i media
├── index.php              # Główny plik aplikacji
└── README.md              # Instrukcje
```

## Instalacja i Konfiguracja

### 1. Wymagania
- Serwer WWW Apache z mod_rewrite
- PHP 7.4 lub wyższa
- MySQL 5.7+ lub MariaDB 10.2+
- FTP lub dostęp SSH do serwera

### 2. Krok po kroku

#### a) Przygotowanie bazy danych
1. Zaloguj się do phpMyAdmin lub konsoli MySQL
2. Stwórz nową bazę danych: `biblioteka_szkolna`
3. Zaimportuj plik `database/schema.sql`

```bash
mysql -u root -p biblioteka_szkolna < database/schema.sql
```

#### b) Konfiguracja połączenia
1. Otwórz plik `database/config.php`
2. Ustaw dane dostępu do bazy danych:
```php
define('DB_HOST', 'localhost');        // Host bazy
define('DB_USER', 'root');             // Użytkownik MySQL
define('DB_PASSWORD', '');             // Hasło MySQL
define('DB_NAME', 'biblioteka_szkolna'); // Nazwa bazy
```

#### c) Przesłanie plików na serwer
- Załaduj wszystkie pliki projektu do katalogu publicznego serwera WWW
- Najlepiej w katalogu: `/var/www/html/biblioteka/` lub podobnym

#### d) Uprawnienia katalogów
```bash
chmod 755 images/
chmod 755 admin/
chmod 755 website/
```

### 3. Logowanie do panelu administracyjnego

**Dane domyślne:**
- Login: `admin`
- Hasło: `admin123`

⚠️ **WAŻNE:** Zmień hasło administratora w panelu - `Ustawienia`

## Funkcjonalności

### Strona Publiczna (Frontend)

1. **Strona Główna**
   - Animowany baner z co najmniej 3 zdjęciami
   - Widgety: pogoda, kalendarz, losowe zdjęcie
   - Lista najnowszych artykułów/newsów
   - Animowane menu boczne

2. **Wyszukiwanie Książek**
   - Wyszukiwanie po tytule, autorze, roku wydania
   - Wyświetlanie dostępnych kopii
   - Informacja o statusie książki

3. **Księga Gości**
   - Formularz do dodawania opinii
   - Moderacja wpisów (zatwierdzanie/odrzucanie)
   - Wyświetlanie zatwierdzonych wpisów

4. **Kontakt**
   - Formularz kontaktowy z kategoriami
   - Wysyłanie e-maili do biblioteki
   - Potwierdzenie wysłania dla użytkownika

5. **Artykuły**
   - Pełny widok artykułów
   - Licznik wyświetleń
   - Autor i data publikacji

### Panel Administracyjny

1. **Pulpit (Dashboard)**
   - Statystyka: książki, uczniowie, wypożyczenia, artykuły
   - Liczba oczekujących wpisów w księdze gości

2. **Zarządzanie Książkami**
   - Dodawanie, edycja, usuwanie
   - Informacje: tytuł, autor, wydawnictwo, ISBN, rok
   - Liczba dostępnych kopii
   - Status aktywności (dostępna/niedostępna)

3. **Zarządzanie Uczniami**
   - CRUD dla uczniów
   - Dane: imię, nazwisko, PESEL, e-mail, klasa
   - Wyświetlanie wypożyczeń ucznia

4. **Zarządzanie Wypożyczeniami**
   - Dodawanie nowych wypożyczeń
   - Zwracanie książek
   - Historia wypożyczeń
   - Oznaczanie książek przetrzymywanych

5. **Raporty**
   - Książki przetrzymywane powyżej danego okresu
   - Aktywność uczniów
   - Statystyka dostępności książek

6. **Zarządzanie Artykułami**
   - Tworzenie, edycja, usuwanie newsów
   - Publikacja/schowanie artykułów
   - Dodawanie zdjęć do artykułów

7. **Zarządzanie Banerami**
   - Dodawanie/edycja banerów do carouselu
   - Ustawianie kolejności wyświetlania
   - Włączanie/wyłączanie banerów

8. **Księga Gości**
   - Zatwierdzanie wpisów do publikacji
   - Odrzucanie wpisów
   - Usuwanie opublikowanych wpisów

9. **Ustawienia**
   - Informacje o systemie
   - Statystyka bazy danych

## Baza Danych - Schemat

### Tabela `admini`
- `id` - identyfikator administratora
- `login` - login do systemu
- `haslo` - hasło (zalecane szyfrowanie md5 lub bcrypt)
- `email` - e-mail administratora
- `imie`, `nazwisko` - dane osobowe
- `aktywny` - flaga aktywności

### Tabela `ksiazki`
- `id` - identyfikator książki
- `tytul` - tytuł
- `autor` - autor
- `wydawnictwo` - wydawnictwo
- `rok_wydania` - rok publikacji
- `isbn` - numer ISBN
- `aktywna` - czy dostępna do wypożyczenia
- `ilosc_kopii` - liczba kopii w bibliotece
- `uwagi` - notatki

### Tabela `uczniowie`
- `id` - identyfikator ucznia
- `imie`, `nazwisko` - dane
- `pesel` - PESEL
- `email` - e-mail
- `klasa` - klasa ucznia
- `uwagi` - notatki
- `aktywny` - flaga aktywności

### Tabela `wypozyczenia`
- `id` - identyfikator wypożyczenia
- `id_ksiazki` - referencja do książki
- `id_ucznia` - referencja do ucznia
- `data_wypozyczenia` - kiedy wypożyczona
- `data_zwrotu` - kiedy zwrócona (NULL jeśli nie zwrócona)
- `data_planowanego_zwrotu` - kiedy powinna być zwrócona
- `uwagi` - notatki

### Tabela `news`
- `id` - identyfikator artykułu
- `tytul` - tytuł
- `wstep` - krótka zapowiedź
- `tresc` - pełna treść
- `autor` - autor artykułu
- `zdjecie` - nazwa zdjęcia
- `opublikowany` - czy widoczny
- `ilosc_wyswietlen` - liczba wyświetleń
- `data_publikacji` - data dodania

### Tabela `banery`
- `id` - identyfikator banera
- `sciezka_zdjecia` - ścieżka do zdjęcia
- `tytul` - tytuł banera
- `opis` - opis
- `kolejnosc` - kolejność wyświetlania
- `aktywny` - czy widoczny

### Tabela `ksiegi_gosci`
- `id` - identyfikator wpisu
- `nick` - nick autora
- `email` - e-mail autora
- `tresc` - treść wpisu
- `data_dodania` - kiedy dodane
- `widoczny` - czy opublikowane

### Tabela `ksiegi_gosci_pending`
- `id` - identyfikator wpisu
- `nick`, `email`, `tresc` - dane wpisu
- `data_dodania` - kiedy dodane
- `zatwierdzony` - status zatwierdzenia

## Zmienne i Stałe

### Stałe globalne (database/config.php)
```php
DB_HOST          // Host bazy danych
DB_USER          // Użytkownik bazy
DB_PASSWORD      // Hasło do bazy
DB_NAME          // Nazwa bazy danych
DB_CHARSET       // Kodowanie (utf8mb4)
LIBRARY_EMAIL    // E-mail biblioteki
LIBRARY_NAME     // Nazwa biblioteki
LIBRARY_ADDRESS  // Adres
LIBRARY_PHONE    // Telefon
DEFAULT_LOAN_PERIOD // Domyślny okres wypożyczenia (dni)
BASE_PATH        // Ścieżka bazowa projektu
ADMIN_PATH       // Ścieżka do admin
WEBSITE_PATH     // Ścieżka do website
STATIC_PATH      // Ścieżka do static
```

### Zmienne Sesji
```php
$_SESSION['admin_id']        // ID zalogowanego admina
$_SESSION['admin_login']     // Login admina
$_SESSION['admin_email']     // E-mail admina
$_SESSION['admin_imie']      // Imię admina
$_SESSION['admin_nazwisko']  // Nazwisko admina
```

## Walidacja i Bezpieczeństwo

1. **Sanityzacja wejścia**: Używana funkcja `sanitize()` do czyszczenia danych
2. **Prepared Statements**: MySQLi prepared statements do zapobiegania SQL injection
3. **Hasła**: Zalecane szyfrowanie hasłem `password_hash()`
4. **Sesje**: Obsługa sesji PHP z timeoutem
5. **CSRF**: Podstawowa ochrona poprzez sesje

## Rozwój i Rozszerzenie

Możliwe dodatki i ulepszenia:

1. ✅ Raport uczniów z filtrem po nazwisku
2. ✅ Raport książek przetrzymywanych
3. ✅ Animowany baner (carousel)
4. ✅ Wersja mobilna (responsive)
5. ⭕ Rezerwacje książek
6. ⭕ System notyfikacji e-mail
7. ⭕ Export raportów do PDF/Excel
8. ⭕ Integracja z kalendarkiem (zarezerwowane terminy)
9. ⭕ Oceny i recenzje książek
10. ⭕ Aplikacja mobilna

## Testy

### Konta Testowe
- **Admin**: login: `admin`, hasło: `admin123`
- Uczniowie testowi: Wstępnie załadowani w bazie

### Scenariusze Testowania
1. Logowanie do panelu administracyjnego
2. Dodawanie/edycja/usuwanie książek
3. Wyszukiwanie książek na stronie publicznej
4. Wypełnianie formularza kontaktowego
5. Dodawanie wpisu do księgi gości
6. Zatwierdzanie/odrzucanie wpisów

## Wsparcie i Kontakt

**Szkoła Wesoła Szkoła**
- Adres: ul. Szkolna 1, 54-230 Gdańsk
- E-mail: biblioteka@wesolaszkola.pl
- Telefon: +48 58 123 45 67

---

**Projekt opracowany dla TEB Częstochowa**
Wersja: 1.0
Rok: 2025
