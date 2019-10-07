-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Agu 2019 pada 19.37
-- Versi server: 10.1.30-MariaDB
-- Versi PHP: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mychat`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat`
--

CREATE TABLE `chat` (
  `user_1` int(12) NOT NULL,
  `user_2` int(12) NOT NULL,
  `waktu` datetime NOT NULL,
  `pesan` text NOT NULL,
  `id_chat` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `chat`
--

INSERT INTO `chat` (`user_1`, `user_2`, `waktu`, `pesan`, `id_chat`) VALUES
(1, 4, '2019-06-28 17:07:15', 'Taufik to Bono', 1),
(4, 1, '2019-06-24 20:00:00', 'bono to taufik', 2),
(1, 3, '2019-06-24 10:00:00', 'taufik to isom', 3),
(3, 1, '2019-06-24 20:00:00', 'isom to taufik', 4),
(1, 3, '2019-06-28 04:00:00', 'Text sent by Mochamad Taufikurrohman', 5),
(1, 3, '2019-07-31 06:03:45', 'haloo, ishom. Apa kabar e?', 6),
(1, 3, '2019-07-31 06:46:58', 'Haloo ishom.. kok gak di bales sih..', 7),
(1, 3, '2019-07-31 06:55:21', 'haloo.. gak dibales lagi.. ah', 8),
(3, 1, '2019-08-01 07:57:51', 'halo from ishom', 9),
(1, 3, '2019-08-01 08:59:36', 'hi ishom', 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(12) NOT NULL,
  `nama` varchar(35) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama`, `username`, `password`) VALUES
(1, 'Mochamad Taufikurrohman', 'opick', 'a5553bf55ffd70c92906667b910f6676'),
(2, 'Braja Sindika Laksamana', 'braja', 'e71d27d68e3364baa251b86a7357380a'),
(3, 'Ishomuddin', 'ishom', 'b6b6e6a955147b1e406d6fffcac6c04f'),
(4, 'Bono Green Star', 'bono', '83c2ffa1df5e7a6b37616b8f63e95b17');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id_chat`),
  ADD KEY `user_1` (`user_1`),
  ADD KEY `user_2` (`user_2`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `chat`
--
ALTER TABLE `chat`
  MODIFY `id_chat` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`user_1`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`user_2`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
