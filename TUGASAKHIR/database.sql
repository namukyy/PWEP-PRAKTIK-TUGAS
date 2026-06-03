-- ============================================================
--  DigEdu – Platform Belajar UTBK
--  Database Schema + Sample Data
--  Cara import: buka phpMyAdmin → Import → pilih file ini
-- ============================================================

CREATE DATABASE IF NOT EXISTS digedu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE digedu;

-- ============================================================
-- TABEL 1: users
-- ============================================================
DROP TABLE IF EXISTS forum_replies;
DROP TABLE IF EXISTS forum_threads;
DROP TABLE IF EXISTS tryout_results;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  nama         VARCHAR(100) NOT NULL,
  email        VARCHAR(100) NOT NULL UNIQUE,
  password     VARCHAR(255) NOT NULL,
  role         ENUM('siswa','admin') DEFAULT 'siswa',
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL 2: tryout_results (relasi ke users)
-- ============================================================
CREATE TABLE tryout_results (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL,
  paket        VARCHAR(150) NOT NULL,
  nilai        INT NOT NULL,
  total_soal   INT NOT NULL,
  benar        INT NOT NULL,
  waktu_menit  INT NOT NULL,
  dikerjakan   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL 3: forum_threads
-- ============================================================
CREATE TABLE forum_threads (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL,
  judul        VARCHAR(255) NOT NULL,
  isi          TEXT NOT NULL,
  kategori     ENUM('Matematika','Bahasa Indonesia','Bahasa Inggris','Penalaran Umum','Umum') DEFAULT 'Umum',
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL 4: forum_replies (relasi ke forum_threads & users)
-- ============================================================
CREATE TABLE forum_replies (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  thread_id    INT NOT NULL,
  user_id      INT NOT NULL,
  isi          TEXT NOT NULL,
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (thread_id) REFERENCES forum_threads(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)   REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SAMPLE DATA – users
-- Password semua akun: password123 (di-hash MD5)
-- Login: siswa@digedu.id / password123
-- ============================================================
INSERT INTO users (nama, email, password, role) VALUES
  ('Nabil Muhammad Zaqi',  'siswa@digedu.id',    MD5('password123'), 'siswa'),
  ('Maymunah Azzahra',     'maymunah@digedu.id', MD5('password123'), 'siswa'),
  ('Duta Firmansyah',      'duta@digedu.id',     MD5('password123'), 'siswa');

-- ============================================================
-- SAMPLE DATA – tryout_results (3 data)
-- ============================================================
INSERT INTO tryout_results (user_id, paket, nilai, total_soal, benar, waktu_menit) VALUES
  (1, 'Tryout UTBK #1 – Penalaran Umum',   88, 5, 4, 12),
  (1, 'Tryout UTBK #2 – Pengetahuan Umum', 76, 5, 4, 15),
  (1, 'Tryout UTBK #3 – Matematika',        80, 5, 4, 10),
  (2, 'Tryout UTBK #1 – Penalaran Umum',   60, 5, 3, 18),
  (2, 'Tryout UTBK #3 – Matematika',       100, 5, 5,  8),
  (3, 'Tryout UTBK #2 – Pengetahuan Umum', 80, 5, 4, 14);

-- ============================================================
-- SAMPLE DATA – forum_threads (3 data)
-- ============================================================
INSERT INTO forum_threads (user_id, judul, isi, kategori) VALUES
  (2, 'Cara menentukan diskriminan persamaan kuadrat?',
     'Halo semua, aku masih bingung cara menentukan diskriminan pada persamaan kuadrat ax² + bx + c = 0. Apakah rumusnya D = b² - 4ac? Dan apa artinya jika D > 0, D = 0, dan D < 0?',
     'Matematika'),
  (3, 'Tips membaca cepat untuk soal Bahasa Indonesia UTBK',
     'Bagi yang punya tips membaca cepat untuk mengerjakan soal literasi di UTBK, please share di sini! Aku sering kehabisan waktu saat mengerjakan bagian ini.',
     'Bahasa Indonesia'),
  (1, 'Strategi pengerjaan soal penalaran umum?',
     'Penalaran umum itu kadang bikin pusing. Ada yang punya strategi khusus untuk mengerjakan soal-soal penalaran umum di UTBK? Terutama bagian silogisme dan analogi.',
     'Penalaran Umum');

-- ============================================================
-- SAMPLE DATA – forum_replies (3 data)
-- ============================================================
INSERT INTO forum_replies (thread_id, user_id, isi) VALUES
  (1, 1, 'Betul, D = b² - 4ac. Kalau D > 0 berarti ada 2 akar real berbeda, D = 0 berarti 2 akar real kembar, dan D < 0 berarti tidak ada akar real.'),
  (1, 3, 'Tambahan: nilai diskriminan juga menentukan apakah persamaan bisa difaktorkan. Kalau D adalah kuadrat sempurna, bisa difaktorkan dengan bilangan rasional.'),
  (2, 1, 'Teknik skimming dan scanning sangat membantu. Baca judul, kalimat pertama setiap paragraf dulu, baru baca pertanyaannya.');
