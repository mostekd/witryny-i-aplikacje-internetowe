-- utworzenie bazy danych
create database if not exists biblioteka_szkolna
  default character set utf8mb4
  collate utf8mb4_general_ci;

use biblioteka_szkolna;

-- tabela administratorów
create table if not exists admin (
  id int unsigned not null auto_increment,
  username varchar(100) not null unique,
  password_hash varchar(255) not null,
  email varchar(150) null,
  full_name varchar(200) null,
  created_at datetime not null default current_timestamp,
  last_login datetime null,
  primary key (id)
) engine=innodb default charset=utf8mb4;

-- tabela książek
create table if not exists books (
  id int unsigned not null auto_increment,
  title varchar(500) not null,
  author varchar(300) null,
  publisher varchar(255) null,
  year smallint unsigned null,
  isbn varchar(30) null,
  active tinyint(1) not null default 1,
  notes text null,
  cover_image varchar(255) null,
  created_at datetime not null default current_timestamp,
  updated_at datetime null on update current_timestamp,
  primary key (id),
  index (title),
  index (author),
  index (isbn)
) engine=innodb default charset=utf8mb4;

-- tabela uczniów
create table if not exists students (
  id int unsigned not null auto_increment,
  first_name varchar(120) not null,
  last_name varchar(120) not null,
  pesel varchar(20) null,
  email varchar(150) null,
  notes text null,
  created_at datetime not null default current_timestamp,
  primary key (id),
  index (last_name),
  index (pesel)
) engine=innodb default charset=utf8mb4;

-- tabela wypożyczeń
create table if not exists loans (
  id int unsigned not null auto_increment,
  book_id int unsigned not null,
  student_id int unsigned not null,
  date_borrowed datetime not null default current_timestamp,
  date_due date null,
  date_returned datetime null,
  returned tinyint(1) not null default 0,
  notes text null,
  created_by_admin int unsigned null,
  primary key (id),
  foreign key (book_id) references books(id) on delete restrict on update cascade,
  foreign key (student_id) references students(id) on delete restrict on update cascade,
  foreign key (created_by_admin) references admin(id) on delete set null on update cascade,
  index (book_id),
  index (student_id),
  index (date_borrowed)
) engine=innodb default charset=utf8mb4;

-- historia wypożyczeń (rejestr wszystkich operacji na wypożyczeniach)
create table if not exists loan_history (
  id int unsigned not null auto_increment,
  loan_id int unsigned not null,
  book_id int unsigned not null,
  student_id int unsigned not null,
  admin_id int unsigned null,
  action enum('borrow','extend','return') not null,
  action_date datetime not null default current_timestamp,
  notes text null,
  primary key (id),
  foreign key (loan_id) references loans(id) on delete cascade on update cascade,
  foreign key (book_id) references books(id) on delete cascade on update cascade,
  foreign key (student_id) references students(id) on delete cascade on update cascade,
  foreign key (admin_id) references admin(id) on delete set null on update cascade,
  index (loan_id),
  index (student_id),
  index (action)
) engine=innodb default charset=utf8mb4;

-- tabela news (wpisy / artykuły)
create table if not exists news (
  id int unsigned not null auto_increment,
  title varchar(300) not null,
  slug varchar(300) not null unique,
  excerpt text null,
  content longtext not null,
  author varchar(200) null,
  published_at datetime null,
  is_published tinyint(1) not null default 0,
  created_at datetime not null default current_timestamp,
  updated_at datetime null on update current_timestamp,
  primary key (id),
  index (is_published),
  index (published_at)
) engine=innodb default charset=utf8mb4;

-- tabela obrazów
create table if not exists images (
  id int unsigned not null auto_increment,
  file_name varchar(255) not null,
  alt_text varchar(255) null,
  title varchar(255) null,
  uploaded_by int unsigned null,
  uploaded_at datetime not null default current_timestamp,
  description text null,
  primary key (id),
  foreign key (uploaded_by) references admin(id) on delete set null on update cascade
) engine=innodb default charset=utf8mb4;

-- tabela księgi gości
create table if not exists guestbook_entries (
  id int unsigned not null auto_increment,
  nickname varchar(100) not null,
  email varchar(150) null,
  message text not null,
  created_at datetime not null default current_timestamp,
  approved tinyint(1) not null default 0,
  approved_by int unsigned null,
  approved_at datetime null,
  primary key (id),
  foreign key (approved_by) references admin(id) on delete set null on update cascade,
  index (approved)
) engine=innodb default charset=utf8mb4;

-- tabela kontaktów
create table if not exists contacts (
  id int unsigned not null auto_increment,
  topic enum('zapytanie o dostępność książki','prośba o rezerwację','inna sprawa') not null default 'inna sprawa',
  first_name varchar(120) null,
  last_name varchar(120) null,
  email varchar(150) not null,
  message text not null,
  sent_at datetime not null default current_timestamp,
  processed tinyint(1) not null default 0,
  processed_by int unsigned null,
  processed_at datetime null,
  primary key (id),
  foreign key (processed_by) references admin(id) on delete set null on update cascade
) engine=innodb default charset=utf8mb4;

-- tabela ustawień systemu
create table if not exists settings (
  `key` varchar(100) not null primary key,
  `value` varchar(500) null,
  updated_at datetime not null default current_timestamp on update current_timestamp
) engine=innodb default charset=utf8mb4;

insert ignore into settings (`key`,`value`) values ('loan_period_days','14');

-- tabela logów administracyjnych
create table if not exists admin_logs (
  id int unsigned not null auto_increment,
  admin_id int unsigned null,
  action varchar(255) not null,
  ip_address varchar(45) null,
  user_agent varchar(255) null,
  created_at datetime not null default current_timestamp,
  primary key (id),
  foreign key (admin_id) references admin(id) on delete set null on update cascade,
  index (admin_id)
) engine=innodb default charset=utf8mb4;

-- tabela historii zmian
create table if not exists history_changes (
  id int unsigned not null auto_increment,
  table_name varchar(100) not null,
  record_id int unsigned not null,
  admin_id int unsigned null,
  action enum('insert','update','delete') not null,
  field_name varchar(100) null,
  old_value text null,
  new_value text null,
  created_at datetime not null default current_timestamp,
  primary key (id),
  foreign key (admin_id) references admin(id) on delete set null on update cascade,
  index (table_name),
  index (record_id),
  index (action)
) engine=innodb default charset=utf8mb4;

-- historia logowań (ostatnie 30 dni)
create table if not exists login_history (
  id int unsigned not null auto_increment,
  user_type enum('admin','student') not null,
  user_id int unsigned not null,
  ip_address varchar(45) null,
  user_agent varchar(255) null,
  success tinyint(1) not null default 1,
  created_at datetime not null default current_timestamp,
  primary key (id),
  index (user_type),
  index (user_id),
  index (created_at)
) engine=innodb default charset=utf8mb4;

alter table students
add column login varchar(100) unique not null,
add column password_hash varchar(255) not null;

alter table students
add column last_login datetime null after email;


-- (opcja: czyszczenie starszych niż 30 dni)
-- delete from login_history where created_at < (now() - interval 30 day);
