
-- baza danych: biblioteka_szkolna
-- autor: Dawid Mostowski
-- data utworzenia: 2025-10-17

create database if not exists biblioteka_szkolna character set utf8mb4 collate utf8mb4_unicode_ci;
use biblioteka_szkolna;

-- tabela administratorów
create table if not exists administrator (
    id int auto_increment primary key,
    login varchar(50) not null unique,
    haslo varchar(255) not null,
    email varchar(100) not null,
    data_utworzenia datetime not null default current_timestamp
);

-- tabela ról (możliwość rozbudowy uprawnień w przyszłości)
create table if not exists role (
    id int auto_increment primary key,
    nazwa varchar(50) not null unique
);

-- tabela powiązań administrator-rola
create table if not exists administrator_role (
    id int auto_increment primary key,
    administrator_id int not null,
    rola_id int not null,
    foreign key (administrator_id) references administrator(id) on delete cascade,
    foreign key (rola_id) references role(id) on delete cascade
);

-- tabela zdjęć do banera i newsów
create table if not exists zdjecia (
    id int auto_increment primary key,
    sciezka varchar(255) not null,
    opis varchar(255) default null,
    do_banera bool default 0,
    data_dodania datetime not null default current_timestamp
);

-- tabela newsów (wpisów na blogu)
create table if not exists news (
    id int auto_increment primary key,
    tytul varchar(200) not null,
    data_publikacji datetime not null default current_timestamp,
    wstep text not null,
    tresc text not null,
    autor varchar(100) not null,
    zdjecie_id int default null,
    foreign key (zdjecie_id) references zdjecia(id) on delete set null
);

-- tabela kategorii książek
create table if not exists kategorie (
    id int auto_increment primary key,
    nazwa varchar(100) not null unique
);

-- tabela książek
create table if not exists ksiazki (
    id int auto_increment primary key,
    tytul varchar(200) not null,
    autor varchar(150) not null,
    wydawnictwo varchar(100) not null,
    rok_wydania year not null,
    isbn varchar(20) not null,
    aktywna bool default 1,
    uwagi text,
    kategoria_id int default null,
    zdjecie_id int default null,
    foreign key (kategoria_id) references kategorie(id) on delete set null,
    foreign key (zdjecie_id) references zdjecia(id) on delete set null
);

-- tabela uczniów
create table if not exists uczniowie (
    id int auto_increment primary key,
    imie varchar(50) not null,
    nazwisko varchar(80) not null,
    pesel varchar(11) not null unique,
    email varchar(100) not null,
    klasa varchar(20) default null,
    uwagi text
);

-- tabela wypożyczeń
create table if not exists wypozyczenia (
    id int auto_increment primary key,
    ksiazka_id int not null,
    uczen_id int not null,
    data_wypozyczenia date not null,
    data_zwrotu date default null,
    status enum('wypozyczona','zwrócona','przeterminowana') not null default 'wypozyczona',
    uwagi text,
    foreign key (ksiazka_id) references ksiazki(id) on delete cascade,
    foreign key (uczen_id) references uczniowie(id) on delete cascade
);

-- tabela księgi gości
create table if not exists ksiega_gosci (
    id int auto_increment primary key,
    tresc text not null,
    nick varchar(50) not null,
    email varchar(100) not null,
    data_dodania datetime not null default current_timestamp,
    zatwierdzony bool default 0,
    odrzucony bool default 0,
    admin_id int default null,
    data_akceptacji datetime default null,
    foreign key (admin_id) references administrator(id) on delete set null
);

-- tabela ustawień (np. domyślny okres wypożyczenia)
create table if not exists ustawienia (
    id int auto_increment primary key,
    klucz varchar(50) not null unique,
    wartosc varchar(100) not null
);

-- tabela logów operacji administracyjnych
create table if not exists logi_admin (
    id int auto_increment primary key,
    admin_id int not null,
    akcja varchar(255) not null,
    data_operacji datetime not null default current_timestamp,
    foreign key (admin_id) references administrator(id) on delete cascade
);

-- tabela wydarzeń kalendarza
create table if not exists kalendarz_wydarzenia (
    id int auto_increment primary key,
    uzytkownik_id int not null,
    tytul varchar(200) not null,
    opis text,
    data_rozpoczecia datetime not null,
    data_zakonczenia datetime,
    typ enum('wypozyczenie', 'zwrot', 'wlasne') not null default 'wlasne',
    kolor varchar(7) default '#3949ab',
    foreign key (uzytkownik_id) references uczniowie(id) on delete cascade
);

-- tabela preferencji użytkowników (w tym lokalizacja dla pogody)
create table if not exists preferencje_uzytkownika (
    id int auto_increment primary key,
    uzytkownik_id int not null,
    lokalizacja_lat decimal(10,8),
    lokalizacja_lon decimal(11,8),
    lokalizacja_miasto varchar(100),
    ostatnia_aktualizacja_pogody datetime,
    jezyk varchar(5) default 'pl_PL',
    foreign key (uzytkownik_id) references uczniowie(id) on delete cascade
);

-- przykładowe dane
insert into role (nazwa) values ('superadmin'),('redaktor');
insert into administrator (login, haslo, email) values ('admin', '$2y$10$przykladowyhashhasla', 'admin@wesolaszkola.pl');
insert into administrator_role (administrator_id, rola_id) values (1, 1);
insert into ustawienia (klucz, wartosc) values ('okres_wypozyczenia', '14');
