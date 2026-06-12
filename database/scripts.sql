CREATE DATABASE bibliotheque;

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(225) NOT NULL,
  `photo` varchar(255) DEFAULT 'default.png',
  `role` enum('user','admin') DEFAULT 'user',
  `date_creation` datetime DEFAULT curdate()
)

CREATE TABLE `livre` (
  `id_livre` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `auteur` varchar(255) NOT NULL,
  `stock_total` int(11) NOT NULL,
  `stock_dispo` int(11) NOT NULL
)

CREATE TABLE `emprunter` (
  `id_emprunt` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(11) NOT NULL,
  `id_livre` int(11) NOT NULL,
  `date_demande` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_retour` date DEFAULT NULL,
  `statut` enum('en_attente','confirme','rendu') DEFAULT 'en_attente',
  FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur`(`id_utilisateur`) ON DELETE CASCADE,
  FOREIGN KEY (`id_livre`) REFERENCES `livre`(`id_livre`) ON DELETE CASCADE
)

-- Un compte admin est crée directement pour accès unique
INSERT INTO utilisateur (username, email, password, photo, role, date_creation ) VALUES
( 'admin', 'admin@gestbiblio.com', 'admin123', 'default.png', 'admin', NOW());