-- Database: Manpro

-- DROP DATABASE IF EXISTS "Manpro";

DROP TABLE IF EXISTS Owner
DROP TABLE IF EXISTS DetailTransaksi
DROP TABLE IF EXISTS Komponen
DROP TABLE IF EXISTS Transaksi
DROP TABLE IF EXISTS Furnitur
DROP TABLE IF EXISTS Furnitur_Komponen

DROP TABLE IF EXISTS Pelanggan

DROP TABLE IF EXISTS Kelurahan

DROP TABLE IF EXISTS Kecamatan

--------------------------------------------

CREATE TABLE Owner (
    idPemilik INT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255)
);


SELECT * FROM Owner

--------------------------------------------------------------------
INSERT INTO Owner (idPemilik, username, password) VALUES
(1, 'admin', 'qwerty');
--------------------------------------------------------------------

INSERT INTO Kecamatan (idKecamatan, nama) VALUES
(1, 'Andir'),
(2, 'Astanaanyar'),
(3, 'Antapani'),
(4, 'Arcamanik'),
(5, 'Babakan Ciparay'),
(6, 'Bandung Kidul'),
(7, 'Bandung Kulon'),
(8, 'Bandung Wetan'),
(9, 'Batununggal'),
(10, 'Bojongloa Kaler'),
(11, 'Bojongloa Kidul'),
(12, 'Buahbatu'),
(13, 'Cibeunying Kaler'),
(14, 'Cibeunying Kidul'),
(15, 'Cibiru'),
(16, 'Cicendo'),
(17, 'Cidadap'),
(18, 'Cinambo'),
(19, 'Coblong'),
(20, 'Gedebage'),
(21, 'Kiaracondong'),
(22, 'Lengkong'),
(23, 'Mandalajati'),
(24, 'Panyileukan'),
(25, 'Rancasari'),
(26, 'Regol'),
(27, 'Sukajadi'),
(28, 'Sukasari'),
(29, 'Sumur Bandung'),
(30, 'Ujung Berung');

--------------------------------------------------------------------

INSERT INTO Kelurahan (idKelurahan, nama, idKecamatan) VALUES
(1, 'Ciroyom', 1),         -- Andir
(2, 'Garuda', 1),          -- Andir
(3, 'Kebon Jeruk', 1),     -- Andir
(4, 'Maleber', 1),         -- Andir
(5, 'Dunguscariang', 1),   -- Andir
(6, 'Cempaka', 2),         -- Astanaanyar
(7, 'Karasak', 2),         -- Astanaanyar
(8, 'Nyengseret', 2),      -- Astanaanyar
(9, 'Panjunan', 2),        -- Astanaanyar
(10, 'Pelindung Hewan', 2),-- Astanaanyar
(11, 'Cisaranten Bina Harapan', 3), -- Antapani
(12, 'Antapani Kidul', 3),           -- Antapani
(13, 'Antapani Tengah', 3),          -- Antapani
(14, 'Antapani Wetan', 3),           -- Antapani
(15, 'Sukamiskin', 4),     -- Arcamanik
(16, 'Cisaranten Kulon', 4), -- Arcamanik
(17, 'Cisaranten Endah', 4), -- Arcamanik
(18, 'Sukamiskin', 4),     -- Arcamanik
(19, 'Cibaduyut', 5),      -- Babakan Ciparay
(20, 'Cirangrang', 5),     -- Babakan Ciparay
(21, 'Margasuka', 5),      -- Babakan Ciparay
(22, 'Babakan Ciparay', 5),-- Babakan Ciparay
(23, 'Kopo', 5),           -- Babakan Ciparay
(24, 'Mengger', 6),        -- Bandung Kidul
(25, 'Wates', 6),          -- Bandung Kidul
(26, 'Batununggal', 6),    -- Bandung Kidul
(27, 'Kujangsari', 6),     -- Bandung Kidul
(28, 'Cijerah', 7),        -- Bandung Kulon
(29, 'Caringin', 7),       -- Bandung Kulon
(30, 'Cigondewah Kaler', 7), -- Bandung Kulon
(31, 'Cigondewah Rahayu', 7), -- Bandung Kulon
(32, 'Cigondewah Kidul', 7), -- Bandung Kulon
(33, 'Campaka', 8),        -- Bandung Wetan
(34, 'Cihapit', 8),        -- Bandung Wetan
(35, 'Citarum', 8),        -- Bandung Wetan
(36, 'Tamansari', 9),      -- Batununggal
(37, 'Malabar', 9),        -- Batununggal
(38, 'Sukamaju', 9),       -- Batununggal
(39, 'Sukapura', 9),       -- Batununggal
(40, 'Sukaluyu', 10),      -- Bojongloa Kaler
(41, 'Sukahaji', 10),      -- Bojongloa Kaler
(42, 'Cijagra', 11),       -- Bojongloa Kidul
(43, 'Margahayu Utara', 11), -- Bojongloa Kidul
(44, 'Babakan Tarogong', 11), -- Bojongloa Kidul
(45, 'Batununggal', 12),   -- Buahbatu
(46, 'Cijaura', 12),       -- Buahbatu
(47, 'Jatisari', 12),      -- Buahbatu
(48, 'Margacinta', 12),    -- Buahbatu
(49, 'Samoja', 12),        -- Buahbatu
(50, 'Padasuka', 13),      -- Cibeunying Kaler
(51, 'Sukaluyu', 13),      -- Cibeunying Kaler
(52, 'Cigadung', 13),      -- Cibeunying Kaler
(53, 'Cihaurgeulis', 14),  -- Cibeunying Kidul
(54, 'Neglasari', 14),     -- Cibeunying Kidul
(55, 'Sukamaju', 14),      -- Cibeunying Kidul
(56, 'Cibiru Hilir', 15),  -- Cibiru
(57, 'Cipadung', 15),      -- Cibiru
(58, 'Pasirbiru', 15),     -- Cibiru
(59, 'Cisaranten Kidul', 16), -- Cicendo
(60, 'Cibadak', 16),       -- Cicendo
(61, 'Cipedes', 16),       -- Cicendo
(62, 'Cicendo', 16),       -- Cicendo
(63, 'Babakan', 16),       -- Cicendo
(64, 'Hegarmanah', 17),    -- Cidadap
(65, 'Ciumbuleuit', 17),   -- Cidadap
(66, 'Ledeng', 17),        -- Cidadap
(67, 'Babakan Sari', 18),  -- Cinambo
(68, 'Cisurupan', 18),     -- Cinambo
(69, 'Pasir Wangi', 18),   -- Cinambo
(70, 'Cipaganti', 19),     -- Coblong
(71, 'Lebakgede', 19),     -- Coblong
(72, 'Dago', 19),          -- Coblong
(73, 'Sadang Serang', 19), -- Coblong
(74, 'Sekeloa', 19),       -- Coblong
(75, 'Cisaranten Kidul', 20), -- Gedebage
(76, 'Rancabolang', 20),   -- Gedebage
(77, 'Cimincrang', 20),    -- Gedebage
(78, 'Cisaranten Endah', 20), -- Gedebage
(79, 'Binong', 21),        -- Kiaracondong
(80, 'Babakan Sari', 21),  -- Kiaracondong
(81, 'Sukapura', 21),      -- Kiaracondong
(82, 'Sukamaju', 21),      -- Kiaracondong
(83, 'Kebon Jayanti', 21), -- Kiaracondong
(84, 'Sukamiskin', 21),    -- Kiaracondong
(85, 'Lengkong', 22),      -- Lengkong
(86, 'Burangrang', 22),    -- Lengkong
(87, 'Malabar', 22),       -- Lengkong
(88, 'Turangga', 22),      -- Lengkong
(89, 'Kebon Waru', 22),    -- Lengkong
(90, 'Cigending', 23),     -- Mandalajati
(91, 'Pasir Endah', 23),   -- Mandalajati
(92, 'Sindang Jaya', 23),  -- Mandalajati
(93, 'Karang Pamulang', 23), -- Mandalajati
(94, 'Antapani Kidul', 24), -- Panyileukan
(95, 'Cisaranten Kulon', 24), -- Panyileukan
(96, 'Cisaranten Endah', 24), -- Panyileukan
(97, 'Cisaranten Kidul', 24), -- Panyileukan
(98, 'Cijawura', 25),      -- Rancasari
(99, 'Derwati', 25),       -- Rancasari
(100, 'Manjahlega', 25),   -- Rancasari
(101, 'Margasari', 25),    -- Rancasari
(102, 'Margahayu Tengah', 25), -- Rancasari
(103, 'Ciateul', 26),      -- Regol
(104, 'Balonggede', 26),   -- Regol
(105, 'Paledang', 26),     -- Regol
(106, 'Ancol', 26),        -- Regol
(107, 'Nyengseret', 26),   -- Regol
(108, 'Karanganyar', 26),  -- Regol
(109, 'Dago', 27),         -- Sukajadi
(110, 'Pasteur', 27),      -- Sukajadi
(111, 'Cipedes', 27),      -- Sukajadi
(112, 'Sukabungah', 27),   -- Sukajadi
(113, 'Sukarasa', 28),     -- Sukasari
(114, 'Isola', 28),        -- Sukasari
(115, 'Geger Kalong', 28), -- Sukasari
(116, 'Sarijadi', 28),     -- Sukasari
(117, 'Babakan Ciamis', 29), -- Sumur Bandung
(118, 'Merdeka', 29),      -- Sumur Bandung
(119, 'Kebon Pisang', 29), -- Sumur Bandung
(120, 'Braga', 29),        -- Sumur Bandung
(121, 'Padasuka', 30),     -- Ujung Berung
(122, 'Pasanggrahan', 30), -- Ujung Berung
(123, 'Cigending', 30),    -- Ujung Berung
(124, 'Cijawura', 30);     -- Ujung Berung


--------------------------------------------------------------------