Biblioteka Szkolna — instrukcja uruchomienia
=========================================

Krótko:

- Skopiuj katalog do katalogu serwera WWW (np. `htdocs`)
- Zaimportuj `database/database.sql`
- Utwórz katalog `images/` z prawami zapisu
- Stwórz konto administratora (INSERT z hashem)

Szybkie komendy:

```bash
# z poziomu katalogu projektu
mysql -u root -p < database/database.sql
mkdir -p images
chown -R www-data:www-data images
chmod 755 images

# wygeneruj hash hasła (PHP must be available)
php -r "echo password_hash('TwojeHaslo', PASSWORD_DEFAULT).PHP_EOL;"
```

Panel administracyjny: `admin/` — zarządzanie książkami, uczniami, wypożyczeniami, news, obrazy, raporty.

Plik dokumentacji: `DOCUMENTATION.md` (można przekonwertować na PDF np. `pandoc DOCUMENTATION.md -o DOCUMENTATION.pdf`).

Jeśli chcesz, mogę:
- zintegrować stabilną wysyłkę e-mail przez SMTP (PHPMailer)
- przygotować archiwum ZIP projektu i gotowy plik PDF dokumentacji
