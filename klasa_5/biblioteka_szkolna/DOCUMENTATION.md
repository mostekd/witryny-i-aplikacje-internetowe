Wykonawca projektu
-------------------

Imię i nazwisko: [WPISZ IMIĘ I NAZWISKO]
Klasa: [WPISZ KLASĘ]

Opis ogólny wykonania zadania
----------------------------

Projekt to prosta strona internetowa „Biblioteka Szkolna” napisana w PHP (bez frameworków) z bazą danych MySQL. Aplikacja składa się z części publicznej (wyświetlanie newsów, wyszukiwanie książek, księga gości, formularz kontaktowy) oraz panelu administracyjnego (logowanie, zarządzanie książkami, uczniami, wypożyczeniami, newsami, obrazami oraz raportami).

Użyte technologie
-----------------
- Backend: PHP (proceduralno-obiektowo), MySQL (InnoDB)
- Frontend: HTML5, CSS3, prosty JavaScript (vanilla)
- Serwer: Apache (np. XAMPP / LAMP)

Projekt bazy danych
-------------------

Plik z pełnym schematem i poleceniami SQL znajduje się w `database/database.sql`.
Główne tabele (krótki opis):

- `admin` — administratorzy (id, username, password_hash, email, full_name, created_at, last_login)
- `books` — księgozbiór (id, title, author, publisher, year, isbn, active, notes, cover_image)
- `students` — uczniowie (id, first_name, last_name, pesel, email, login, password_hash, notes)
- `loans` — wypożyczenia (id, book_id, student_id, date_borrowed, date_due, date_returned, returned, created_by_admin)
- `loan_history` — historia operacji na wypożyczeniach
- `news` — aktualności / wpisy na stronie (id, title, slug, excerpt, content, author, published_at, is_published)
- `images` — obrazy przesyłane do serwisu (id, file_name, alt_text, title, uploaded_by, uploaded_at)
- `guestbook_entries` — wpisy do księgi gości (moderowane)
- `contacts` — zapisane wiadomości z formularza kontaktowego
- `settings` — proste ustawienia, m.in. `loan_period_days`

Klucze i relacje
-----------------
- `loans.book_id` -> `books.id`
- `loans.student_id` -> `students.id`
- `loan_history.loan_id` -> `loans.id`
- `images.uploaded_by` -> `admin.id`
- `guestbook_entries.approved_by` -> `admin.id`

Opis zmiennych i plików ważnych dla uruchomienia
------------------------------------------------
- `database/config.php` — ustawienia połączenia do bazy (DB_HOST, DB_USER, DB_PASS, DB_NAME)
- `database/database.sql` — plik do zaimportowania zawierający schemat bazy i domyślne ustawienia
- `website/` — katalog publiczny użytkownika
- `admin/` — panel administracyjny
- `images/` — katalog z przesłanymi obrazami (trzeba nadać prawa zapisu dla serwera WWW)

Instrukcja uruchomienia lokalnie
--------------------------------
1. Skopiuj folder projektu do katalogu serwera WWW (np. `htdocs` dla XAMPP).
2. Utwórz bazę danych i zaimportuj `database/database.sql`:

```bash
mysql -u root -p < database/database.sql
```

3. Sprawdź `database/config.php` i dopasuj dane dostępu do bazy.
4. Upewnij się, że katalog `images/` istnieje i ma prawa zapisu przez serwer WWW:

```bash
mkdir -p images
chown -R www-data:www-data images
chmod 755 images
```

5. Dodaj konto administratora (wstaw rekord do tabeli `admin` z zaszyfrowanym hasłem):

```bash
php -r "echo password_hash('TwojeHaslo', PASSWORD_DEFAULT).PHP_EOL;"
# następnie użyj tego hasha w INSERT INTO admin ...
```

6. Otwórz w przeglądarce: `http://localhost/<ścieżka>/website/index.php` oraz panel admin: `http://localhost/<ścieżka>/admin/`.

Testy funkcjonalne (co sprawdzić)
--------------------------------
- Logowanie administratora
- Dodawanie / edycja / usuwanie książek
- Dodawanie uczniów i sprawdzanie wypożyczeń
- Dodawanie wpisu do księgi gości (po zatwierdzeniu przez admina powinien się pojawić)
- Formularz kontaktu — zapis do bazy i próba wysłania e-mail (funkcja `mail()` musi mieć działający MTA)
- Panel `admin/images.php` — przesyłanie obrazów do banera
- Raporty: `admin/reports.php` — zmiana domyślnego okresu wypożyczenia i lista przetrzymanych książek

Problemy / uwagi
----------------
- Wysyłka e-maili używa funkcji natywnej `mail()` — może wymagać konfiguracji MTA. Mogę zintegrować PHPMailer z SMTP na życzenie.
- Projekt nie używa frameworków — struktura jest prosta i edukacyjna.

Pliki do dostarczenia
---------------------
- cały folder projektu (spakowany `.zip`)
- plik `database/database.sql` (do zaimportowania) — jest w katalogu `database/`
- dokumentacja (ten plik) — można wygenerować PDF z Markdown (np. `pandoc DOCUMENTATION.md -o DOCUMENTATION.pdf`)
