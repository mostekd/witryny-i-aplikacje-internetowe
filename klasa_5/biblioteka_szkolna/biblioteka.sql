-- biblioteka szkolna - initial schema
create database if not exists biblioteka character set utf8mb4 collate utf8mb4_unicode_ci;
use biblioteka;

-- table: admins
create table if not exists admins (
  id int auto_increment primary key,
  login varchar(100) not null unique,
  password_hash varchar(255) not null,
  fullname varchar(255)
);

-- table: books
create table if not exists books (
  id int auto_increment primary key,
  title varchar(255) not null,
  author varchar(255),
  publisher varchar(255),
  year year,
  isbn varchar(50),
  active tinyint(1) default 1,
  notes text,
  cover_image varchar(255)
);

-- table: students
create table if not exists students (
  id int auto_increment primary key,
  first_name varchar(100),
  last_name varchar(100),
  pesel varchar(20),
  email varchar(255),
  notes text
);

-- table: loans
create table if not exists loans (
  id int auto_increment primary key,
  book_id int not null,
  student_id int not null,
  date_borrowed date,
  date_return date,
  foreign key (book_id) references books(id) on delete cascade,
  foreign key (student_id) references students(id) on delete cascade
);

-- table: news
create table if not exists news (
  id int auto_increment primary key,
  title varchar(255) not null,
  content text,
  excerpt text,
  published_at datetime default current_timestamp,
  author varchar(255),
  image varchar(255)
);

-- table: banner_images
create table if not exists banner_images (
  id int auto_increment primary key,
  filename varchar(255) not null,
  alt text,
  ordering int default 0,
  active tinyint(1) default 1
);

-- table: guestbook_entries
create table if not exists guestbook_entries (
  id int auto_increment primary key,
  nick varchar(100),
  email varchar(255),
  message text,
  submitted_at datetime default current_timestamp,
  approved tinyint(1) default 0
);