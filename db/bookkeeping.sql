-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июн 11 2017 г., 21:06
-- Версия сервера: 10.1.21-MariaDB
-- Версия PHP: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `bookkeeping`
--

-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `type` varchar(128) NOT NULL DEFAULT 'Pay'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `category`
--

INSERT INTO `category` (`id`, `name`, `type`) VALUES
(1, 'Еда', 'Pay'),
(2, 'Проезд', 'Pay'),
(4, 'Развлечения', 'Pay'),
(7, 'Лекарства', 'Pay'),
(9, 'Зарплата Денис', 'Income'),
(10, 'Зарплата Леночка', 'Income'),
(11, 'Банк Дувановых', 'Income'),
(13, 'Алкоголь', 'Pay');

-- --------------------------------------------------------

--
-- Структура таблицы `income`
--

CREATE TABLE `income` (
  `id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `description` varchar(256) NOT NULL,
  `category_id` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `income`
--

INSERT INTO `income` (`id`, `amount`, `description`, `category_id`, `date`) VALUES
(108, 409, 'Билеты на балет', 2, '2017-06-01'),
(109, 55, 'Покушать купил', 1, '2017-06-05'),
(111, 53, 'Анекдоты', 4, '2017-06-04'),
(112, 120, 'Ходили в Кино', 4, '2017-06-04'),
(113, 88, 'Банана', 1, '2017-06-05'),
(114, 50, '', 2, '2017-06-08'),
(115, 75, 'На работе купил кофе', 1, '2017-06-09'),
(116, 110, 'В понедельник', 11, '2017-06-11'),
(118, 26000, '', 9, '2017-06-11'),
(119, 12000, '', 10, '2017-06-11');

-- --------------------------------------------------------

--
-- Структура таблицы `pay`
--

CREATE TABLE `pay` (
  `id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `description` varchar(256) NOT NULL,
  `category_id` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `pay`
--

INSERT INTO `pay` (`id`, `amount`, `description`, `category_id`, `date`) VALUES
(108, 409, 'Билеты на балет', 2, '2017-06-01'),
(109, 55, 'Покушать купил', 1, '2017-06-05'),
(111, 53, 'Анекдоты', 4, '2017-06-04'),
(112, 120, 'Ходили в Кино', 4, '2017-06-04'),
(113, 88, 'Банана', 1, '2017-06-05'),
(114, 50, '', 2, '2017-06-08'),
(115, 75, 'На работе купил кофе', 1, '2017-06-09'),
(116, 120, 'Привет', 1, '2017-06-11'),
(119, 60, 'С Кочневым', 13, '2017-06-11');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `pay`
--
ALTER TABLE `pay`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT для таблицы `income`
--
ALTER TABLE `income`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;
--
-- AUTO_INCREMENT для таблицы `pay`
--
ALTER TABLE `pay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
