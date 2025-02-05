-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Янв 24 2025 г., 10:34
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `db_library`
--
CREATE DATABASE `db_library` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `db_library`;

-- --------------------------------------------------------

--
-- Структура таблицы `books`
--

CREATE TABLE IF NOT EXISTS `books` (
  `id_book` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(255) NOT NULL,
  `author` char(255) DEFAULT NULL,
  `genre` char(100) DEFAULT NULL,
  `publication_year` int(11) DEFAULT NULL,
  `language` char(100) DEFAULT NULL,
  `available_copies` int(11) DEFAULT NULL,
  `location` char(255) DEFAULT NULL,
  PRIMARY KEY (`id_book`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `books`
--

INSERT INTO `books` (`id_book`, `title`, `author`, `genre`, `publication_year`, `language`, `available_copies`, `location`) VALUES
(1, '1984', 'Джордж Оруэлл', 'Дистопия', 1949, 'Русский', 5, 'Полка 1, ряд 2'),
(2, 'Война и мир', 'Лев Толстой', 'Исторический роман', 1869, 'Русский', 3, 'Полка 2, ряд 1'),
(3, 'Гарри Поттер и философский камень', 'Дж.К. Роулинг', 'Фэнтези', 1997, 'Русский', 7, 'Полка 3, ряд 4'),
(4, 'Убить пересмешника', 'Харпер Ли', 'Роман', 1960, 'Русский', 2, 'Полка 1, ряд 3'),
(5, 'Мастер и Маргарита', 'Михаил Булгаков', 'Роман', 1967, 'Русский', 4, 'Полка 2, ряд 2');

-- --------------------------------------------------------

--
-- Структура таблицы `electronic_resources`
--

CREATE TABLE IF NOT EXISTS `electronic_resources` (
  `id_electronic_resource` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `publication_year` date DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_electronic_resource`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `electronic_resources`
--

INSERT INTO `electronic_resources` (`id_electronic_resource`, `title`, `author`, `type`, `publication_year`, `url`) VALUES
(1, 'Основы программирования', 'Иван Иванов', 'Книга', '2020-01-15', 'http://example.com/book1'),
(2, 'Введение в базы данных', 'Петр Петров', 'Книга', '2021-03-22', 'http://example.com/book2'),
(3, 'Современные технологии', 'Светлана Светлова', 'Статья', '2022-05-10', 'http://example.com/article1'),
(4, 'Курс по машинному обучению', 'Алексей Алексеев', 'Онлайн-курс', '2023-02-18', 'http://example.com/course1'),
(5, 'Научные исследования в области ИТ', NULL, 'Журнал', '2019-07-30', 'http://example.com/journal1');

-- --------------------------------------------------------

--
-- Структура таблицы `extradition`
--

CREATE TABLE IF NOT EXISTS `extradition` (
  `id_extradition` int(11) NOT NULL AUTO_INCREMENT,
  `type_resource` varchar(255) DEFAULT NULL,
  `date_issue` date DEFAULT NULL,
  `date_return` date DEFAULT NULL,
  `id_reader` int(11) DEFAULT NULL,
  `id_reservation` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_extradition`),
  KEY `id_reader` (`id_reader`),
  KEY `id_reservation` (`id_reservation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `journal`
--

CREATE TABLE IF NOT EXISTS `journal` (
  `id_journal` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `publishing_house` varchar(255) DEFAULT NULL,
  `publishing_year` date DEFAULT NULL,
  `language` varchar(100) DEFAULT NULL,
  `nunber_copies` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_journal`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `journal`
--

INSERT INTO `journal` (`id_journal`, `title`, `publishing_house`, `publishing_year`, `language`, `nunber_copies`, `location`) VALUES
(1, 'Наука и жизнь', 'Научное издательство', '2020-01-15', 'Русский', 10, 'Полка А, ряд 1'),
(2, 'Мир технологий', 'ТехноПресс', '2021-03-22', 'Русский', 5, 'Полка Б, ряд 2'),
(3, 'Культура и искусство', 'Культурное издательство', '2019-07-30', 'Русский', 8, 'Полка В, ряд 3'),
(4, 'Здоровье и фитнес', 'ФитнесИздатель', '2022-05-10', 'Русский', 6, 'Полка Г, ряд 4'),
(5, 'Экономика сегодня', 'Экономическое издательство', '2023-02-18', 'Русский', 4, 'Полка Д, ряд 5');

-- --------------------------------------------------------

--
-- Структура таблицы `readers`
--

CREATE TABLE IF NOT EXISTS `readers` (
  `id_reader` int(11) NOT NULL AUTO_INCREMENT,
  `fio` varchar(255) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `abress` varchar(255) DEFAULT NULL,
  `number_phone` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  PRIMARY KEY (`id_reader`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `readers`
--

INSERT INTO `readers` (`id_reader`, `fio`, `birthdate`, `abress`, `number_phone`, `email`, `registration_date`) VALUES
(1, 'Гузалия', NULL, NULL, NULL, 'gareeva.guzaliya04@gmail.com', '2025-01-23'),
(2, 'Данияр', NULL, NULL, NULL, 'gareeva.albinka@yandex.ru', '2025-01-23');

-- --------------------------------------------------------

--
-- Структура таблицы `reservation`
--

CREATE TABLE IF NOT EXISTS `reservation` (
  `id_reservation` int(11) NOT NULL AUTO_INCREMENT,
  `fio` varchar(255) DEFAULT NULL,
  `type_resource` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `date_reservation` date DEFAULT NULL,
  `id_reader` int(11) DEFAULT NULL,
  `id_electronic_resource` int(11) DEFAULT NULL,
  `id_book` int(11) DEFAULT NULL,
  `id_journal` int(11) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_reservation`),
  KEY `id_reader` (`id_reader`),
  KEY `id_electronic_resource` (`id_electronic_resource`),
  KEY `id_book` (`id_book`),
  KEY `id_journal` (`id_journal`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Дамп данных таблицы `reservation`
--

INSERT INTO `reservation` (`id_reservation`, `fio`, `type_resource`, `title`, `email`, `status`, `date_reservation`, `id_reader`, `id_electronic_resource`, `id_book`, `id_journal`, `count`) VALUES
(20, 'Гузалия Илгизаровна', 'electronic_resource', '1', 'gareeva@yandex.ru', 'выполнено', '2024-12-30', NULL, NULL, NULL, 2, 1),
(21, 'Гареева Гузалия Илгизаровна', 'journal', 'Мир технологий', 'gareeva.albinka@yandex.ru', 'Ожидание', '2024-12-30', NULL, NULL, NULL, 2, 1),
(22, 'Гареева Гузалия Илгизаровна', 'book', '1', 'gareeva.albinka@yandex.ru', 'ожидает', '2024-12-30', NULL, NULL, NULL, 2, 1),
(23, 'k', 'journal', '5', 'GareevaGI@yandex.ru', 'ожидает', '2025-02-06', NULL, NULL, 3, NULL, 1),
(24, 'Гареева Данияр Илгизарович', 'journal', '1', 'gareeva.albinka@yandex.ru', 'выполнено', '2025-01-20', NULL, NULL, 1, NULL, 3),
(25, 'Иванов Иван Иванович', 'journal', 'Здоровье и фитнес', 'gareeva.guzaliya04@gmail.com', 'Ожидание', '2025-01-19', NULL, NULL, NULL, 4, 1);

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `extradition`
--
ALTER TABLE `extradition`
  ADD CONSTRAINT `extradition_ibfk_1` FOREIGN KEY (`id_reader`) REFERENCES `readers` (`id_reader`),
  ADD CONSTRAINT `extradition_ibfk_2` FOREIGN KEY (`id_reservation`) REFERENCES `reservation` (`id_reservation`);

--
-- Ограничения внешнего ключа таблицы `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`id_reader`) REFERENCES `readers` (`id_reader`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`id_electronic_resource`) REFERENCES `electronic_resources` (`id_electronic_resource`),
  ADD CONSTRAINT `reservation_ibfk_3` FOREIGN KEY (`id_book`) REFERENCES `books` (`id_book`),
  ADD CONSTRAINT `reservation_ibfk_4` FOREIGN KEY (`id_journal`) REFERENCES `journal` (`id_journal`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
