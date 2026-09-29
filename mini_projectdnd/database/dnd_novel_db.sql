CREATE DATABASE IF NOT EXISTS dnd_novel_db;
USE dnd_novel_db;

DROP TABLE IF EXISTS products;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL UNIQUE,
    brand VARCHAR(80) NOT NULL DEFAULT 'Penulis Indonesia',
    category VARCHAR(60) NOT NULL DEFAULT 'Fiksi',
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- DATA LENGKAP NOVEL BEST SELLER ASLI INDONESIA
INSERT INTO products (name, brand, category, price, stock, image_url) VALUES
-- === PETUALANGAN, GUNUNG & ALAM ===
(
    '3726 MDPL (Kisah Nyata Pendakian Rinjani)', 
    'Nurwina Sari', 
    'Petualangan & Alam', 
    89000.00, 
    25, 
    'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80'
),
(
    '0 MDPL (Perjalanan Menemukan Titik Terendah)', 
    'Nurwina Sari', 
    'Petualangan & Alam', 
    85000.00, 
    30, 
    'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=600&auto=format&fit=crop&q=80'
),
(
    'Arah Langkah (Ekspedisi Menyusuri Pelosok Nusantara)', 
    'Fiersa Besari', 
    'Petualangan & Alam', 
    92000.00, 
    18, 
    'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600&auto=format&fit=crop&q=80'
),
(
    'Catatan Juang', 
    'Fiersa Besari', 
    'Petualangan & Alam', 
    88000.00, 
    22, 
    'https://images.unsplash.com/photo-1476820865390-c52aeebb9891?w=600&auto=format&fit=crop&q=80'
),
(
    '5 CM (Kisah Perjalanan & Persahabatan di Mahameru)', 
    'Donny Dhirgantoro', 
    'Petualangan & Alam', 
    95000.00, 
    15, 
    'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=600&auto=format&fit=crop&q=80'
),

-- === SASTRA, SEJARAH & DRAMA BERBOBOT ===
(
    'Laut Bercerita (Edisi Hardcover Kolektor)', 
    'Leila S. Chudori', 
    'Fiksi Sejarah & Sastra', 
    115000.00, 
    14, 
    'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600&auto=format&fit=crop&q=80'
),
(
    'Namaku Alam (Bagian Pertama)', 
    'Leila S. Chudori', 
    'Fiksi Sejarah & Sastra', 
    125000.00, 
    16, 
    'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&auto=format&fit=crop&q=80'
),
(
    'Cantik Itu Luka (Edisi Spesial 20 Tahun)', 
    'Eka Kurniawan', 
    'Fiksi Sejarah & Sastra', 
    135000.00, 
    12, 
    'https://images.unsplash.com/photo-1495640388908-05fa85288e61?w=600&auto=format&fit=crop&q=80'
),
(
    'Lelaki Harimau', 
    'Eka Kurniawan', 
    'Fiksi Sejarah & Sastra', 
    78000.00, 
    10, 
    'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=600&auto=format&fit=crop&q=80'
),
(
    'Bumi Manusia (Tetralogi Buru Buku ke-1)', 
    'Pramoedya Ananta Toer', 
    'Fiksi Sejarah & Sastra', 
    145000.00, 
    20, 
    'https://images.unsplash.com/photo-1491841573634-28140fc7ced7?w=600&auto=format&fit=crop&q=80'
),
(
    'Anak Semua Bangsa (Tetralogi Buru Buku ke-2)', 
    'Pramoedya Ananta Toer', 
    'Fiksi Sejarah & Sastra', 
    138000.00, 
    15, 
    'https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=600&auto=format&fit=crop&q=80'
),

-- === KELUARGA, MOTIVASI & INSPIRASI ===
(
    'Sepatu Dahlan (Trilogi Inspirasi Sepatu)', 
    'Khrisna Pabichara', 
    'Drama & Inspirasi', 
    85000.00, 
    18, 
    'https://images.unsplash.com/photo-1532012164546-f432f2e3edd3?w=600&auto=format&fit=crop&q=80'
),
(
    'Surat Kecil untuk Ayah', 
    'Boy Candra', 
    'Drama & Inspirasi', 
    79000.00, 
    28, 
    'https://images.unsplash.com/photo-1509021436665-8f07dbf5bf1d?w=600&auto=format&fit=crop&q=80'
),
(
    'Negeri 5 Menara (Man Jadda Wajada)', 
    'Ahmad Fuadi', 
    'Drama & Inspirasi', 
    98000.00, 
    22, 
    'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=600&auto=format&fit=crop&q=80'
),
(
    'Ranah 3 Warna (Man Shabara Zhafira)', 
    'Ahmad Fuadi', 
    'Drama & Inspirasi', 
    95000.00, 
    19, 
    'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?w=600&auto=format&fit=crop&q=80'
),
(
    'Laskar Pelangi (Edisi Sampul Baru)', 
    'Andrea Hirata', 
    'Drama & Inspirasi', 
    99000.00, 
    35, 
    'https://images.unsplash.com/photo-1476275466078-4007374efbbe?w=600&auto=format&fit=crop&q=80'
),
(
    'Sang Pemimpi', 
    'Andrea Hirata', 
    'Drama & Inspirasi', 
    89000.00, 
    20, 
    'https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=600&auto=format&fit=crop&q=80'
),

-- === SERI DUNIA PARALEL (TERE LIYE) ===
(
    'Bumi (Serial Dunia Paralel #1)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    99000.00, 
    40, 
    'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&auto=format&fit=crop&q=80'
),
(
    'Bulan (Serial Dunia Paralel #2)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    99000.00, 
    38, 
    'https://images.unsplash.com/photo-1532012164546-f432f2e3edd3?w=600&auto=format&fit=crop&q=80'
),
(
    'Matahari (Serial Dunia Paralel #3)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    99000.00, 
    35, 
    'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600&auto=format&fit=crop&q=80'
),
(
    'Bintang (Serial Dunia Paralel #4)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    99000.00, 
    30, 
    'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=600&auto=format&fit=crop&q=80'
),
(
    'Ceros dan Batozar (Serial Dunia Paralel #4.5)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    89000.00, 
    28, 
    'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80'
),
(
    'Komet (Serial Dunia Paralel #5)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    99000.00, 
    32, 
    'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600&auto=format&fit=crop&q=80'
),
(
    'Komet Minor (Serial Dunia Paralel #6)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    99000.00, 
    25, 
    'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&auto=format&fit=crop&q=80'
),
(
    'Selena (Serial Dunia Paralel #7)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    95000.00, 
    30, 
    'https://images.unsplash.com/photo-1495640388908-05fa85288e61?w=600&auto=format&fit=crop&q=80'
),
(
    'Nebula (Serial Dunia Paralel #8)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    95000.00, 
    29, 
    'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=600&auto=format&fit=crop&q=80'
),
(
    'Si Putih (Serial Dunia Paralel #9)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    89000.00, 
    24, 
    'https://images.unsplash.com/photo-1491841573634-28140fc7ced7?w=600&auto=format&fit=crop&q=80'
),
(
    'Lumpu (Serial Dunia Paralel #10)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    95000.00, 
    22, 
    'https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=600&auto=format&fit=crop&q=80'
),
(
    'Bibi Gill (Serial Dunia Paralel #11)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    98000.00, 
    20, 
    'https://images.unsplash.com/photo-1509021436665-8f07dbf5bf1d?w=600&auto=format&fit=crop&q=80'
),
(
    'Sagaras (Serial Dunia Paralel #12)', 
    'Tere Liye', 
    'Fantasi & Petualangan', 
    99000.00, 
    18, 
    'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=600&auto=format&fit=crop&q=80'
),
(
    'Hujan (Kisah Percintaan & Teknologi Masa Depan)', 
    'Tere Liye', 
    'Sci-Fi & Romance', 
    95000.00, 
    45, 
    'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&auto=format&fit=crop&q=80'
),
(
    'Pulang (Aksi & Pertarungan Shadow Economy)', 
    'Tere Liye', 
    'Aksi & Thriller', 
    95000.00, 
    33, 
    'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80'
),
(
    'Pergi (Kelanjutan Kisah Bujang / Si Babi Hutan)', 
    'Tere Liye', 
    'Aksi & Thriller', 
    98000.00, 
    30, 
    'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=600&auto=format&fit=crop&q=80'
),

-- === ROMANCE & DRAMA POPULER INDONESIA ===
(
    'Perahu Kertas', 
    'Dee Lestari', 
    'Romance & Drama', 
    105000.00, 
    25, 
    'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600&auto=format&fit=crop&q=80'
),
(
    'Aroma Karsa (Edisi Eksklusif)', 
    'Dee Lestari', 
    'Fantasi & Petualangan', 
    140000.00, 
    16, 
    'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&auto=format&fit=crop&q=80'
),
(
    'Dilan 1990: Dia adalah Dilanku Tahun 1990', 
    'Pidi Baiq', 
    'Romance & Drama', 
    85000.00, 
    35, 
    'https://images.unsplash.com/photo-1495640388908-05fa85288e61?w=600&auto=format&fit=crop&q=80'
),
(
    'Milea: Suara dari Dilan', 
    'Pidi Baiq', 
    'Romance & Drama', 
    85000.00, 
    28, 
    'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=600&auto=format&fit=crop&q=80'
),
(
    'Garis Waktu (Sebuah Perjalanan Menghapus Luka)', 
    'Fiersa Besari', 
    'Romance & Drama', 
    82000.00, 
    30, 
    'https://images.unsplash.com/photo-1491841573634-28140fc7ced7?w=600&auto=format&fit=crop&q=80'
),
(
    'Konspirasi Alam Semesta', 
    'Fiersa Besari', 
    'Romance & Drama', 
    85000.00, 
    22, 
    'https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=600&auto=format&fit=crop&q=80'
),
(
    'Ayat-Ayat Cinta (Edisi Khusus)', 
    'Habiburrahman El Shirazy', 
    'Romance & Drama', 
    95000.00, 
    20, 
    'https://images.unsplash.com/photo-1509021436665-8f07dbf5bf1d?w=600&auto=format&fit=crop&q=80'
);
