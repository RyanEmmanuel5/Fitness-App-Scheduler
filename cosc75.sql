-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 01:54 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cosc75`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements_tbl`
--

CREATE TABLE `achievements_tbl` (
  `achievement_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `achievement_condition` text NOT NULL,
  `description` text NOT NULL,
  `achievement_type` enum('Daily','Milestone','Performance','EasterEgg') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements_tbl`
--

INSERT INTO `achievements_tbl` (`achievement_id`, `title`, `achievement_condition`, `description`, `achievement_type`) VALUES
(1, 'One Day Wonder', 'Log a workout on a single day', 'Great start! Let\'s make this a habit.', 'Daily'),
(2, 'Two in a Row', 'Work out for two consecutive days', 'You\'re on a streak. Keep going!', 'Daily'),
(3, 'Weekly Warrior', 'Work out at least 5 times in one week', 'You\'re owning your week with consistency.', 'Daily'),
(4, 'Half Year Hero', 'Work out at least once in a week for 26 weeks', 'Half a year of consistency. You\'re unstoppable!', 'Daily'),
(5, 'Year of the Champion', 'Work out at least once in a week for 52 weeks', 'A year of dedication. You\'re a true champion!', 'Daily'),
(6, 'First Step', 'Complete your very first workout', 'Every journey begins with a single step.', 'Milestone'),
(7, 'On A Roll', 'Complete 5 workouts', 'You\'re getting the hang of this. Keep rolling!', 'Milestone'),
(8, 'Level Up', 'Complete 10 workouts', 'Double digits! You\'re gaining momentum!', 'Milestone'),
(9, 'Consistent Hustler', 'Complete 20 workouts', 'Your hard work is showing. Consistency is key!', 'Milestone'),
(10, 'Fitness Enthusiast', 'Complete 50 workouts', 'Halfway to a hundred! Your dedication inspires others.', 'Milestone'),
(11, 'Centurion', 'Complete 100 workouts', 'A century of workouts! You\'re an unstoppable force!', 'Milestone'),
(12, 'The 500 Club', 'Complete 500 workouts', 'You’ve reached a monumental milestone. Keep setting the bar higher!', 'Milestone'),
(13, 'Workout Legend', 'Complete 1000 workouts', 'You’re a legend in the fitness game. Bow down to greatness!', 'Milestone'),
(14, 'Quick Starter', 'Complete a workout lasting 10 minutes', 'Every minute counts! You\'re off to a great start.', 'Performance'),
(15, 'Half-Hour Hero', 'Complete a workout lasting at least 30 minutes.', 'A solid half-hour of effort. Keep it going!', 'Performance'),
(16, 'Endurance Challenger', 'Complete a workout lasting 60 minutes', 'An hour of dedication. Impressive!', 'Performance'),
(17, 'Marathon Spirit', 'Complete a workout lasting 120 minutes', 'Two hours of focus and determination. Incredible!', 'Performance'),
(18, 'Fitness Marathoner', 'Complete a total of 10 hours workout for one month', 'Ten hours of grind! You\'re pushing boundaries.', 'Performance'),
(19, 'Weekend Warrior', 'Complete 3 long workouts (45 minutes or more) in a single weekend.', 'Your weekends are about progress, not rest!', 'Performance'),
(20, 'Night Owl', 'Complete a workout between midnight and 3 AM', 'Burning the midnight oil... or maybe the calories?', 'EasterEgg'),
(21, 'Early Bird', 'Complete a workout before 6 AM', 'The early bird gets the gains!', 'EasterEgg'),
(22, 'Playlist Party', 'Log 5 workouts with unique title', 'You’re turning workouts into a playlist!', 'EasterEgg'),
(23, 'Lucky 7', 'Log a workout on July 7', 'Seven is your lucky number!', 'EasterEgg'),
(24, 'Lunch Crunch', 'Start a workout at 12:34 PM', '1, 2, 3, 4... time for more gains!', 'EasterEgg'),
(25, 'New Year, New Me', 'Log a workout on January 1st', 'Starting the year strong!', 'EasterEgg');

-- --------------------------------------------------------

--
-- Table structure for table `achievement_notifications_tbl`
--

CREATE TABLE `achievement_notifications_tbl` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_tbl`
--

CREATE TABLE `admin_tbl` (
  `A_ID` int(11) NOT NULL,
  `A_USERNAME` varchar(150) NOT NULL,
  `A_EMAIL` varchar(150) NOT NULL,
  `A_PASSWORD` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_tbl`
--

INSERT INTO `admin_tbl` (`A_ID`, `A_USERNAME`, `A_EMAIL`, `A_PASSWORD`) VALUES
(1, 'Admin1', 'admin1@gmail.com', '$2y$10$cTLIiHJ7h3P.Lnv6MfKQY.3yOSww1vTGt8cuh3ajwf1AKbTUiq6CW');

-- --------------------------------------------------------

--
-- Table structure for table `fitness_entries`
--

CREATE TABLE `fitness_entries` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `summary` text NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications_tbl`
--

CREATE TABLE `notifications_tbl` (
  `notification_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_tbl`
--

CREATE TABLE `schedule_tbl` (
  `event_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `schedule_tbl`
--
DELIMITER $$
CREATE TRIGGER `after_first_workout` AFTER INSERT ON `schedule_tbl` FOR EACH ROW BEGIN
    -- Check if this is the user's first workout
    IF (SELECT COUNT(*) FROM schedule_tbl WHERE username = NEW.username) = 1 THEN
        -- Check if the 'One Day Wonder' achievement already exists
        IF NOT EXISTS (
            SELECT 1 
            FROM user_achievements_tbl 
            WHERE username = NEW.username AND achievement_id = 1
        ) THEN
            -- Insert the achievement for the user
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 1, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_new_year_achievement` AFTER INSERT ON `schedule_tbl` FOR EACH ROW BEGIN
    -- Check if the date is January 1st
    IF MONTH(NEW.date) = 1 AND DAY(NEW.date) = 1 THEN
        -- Ensure the user hasn't already unlocked this achievement
        IF NOT EXISTS (
            SELECT 1 
            FROM user_achievements_tbl 
            WHERE username = NEW.username AND achievement_id = 25
        ) THEN
            -- Insert the achievement into user_achievements_tbl
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 25, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_500_club` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE completed_workouts INT;
    DECLARE achievement_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Count the total number of finished workouts for the user
        SELECT COUNT(*) INTO completed_workouts
        FROM schedule_tbl
        WHERE username = NEW.username 
          AND status = 'finished';

        -- Check if the user already has the "The 500 Club" achievement
        SELECT COUNT(*) INTO achievement_count
        FROM user_achievements_tbl
        WHERE username = NEW.username 
          AND achievement_id = 12;  -- Assuming '12' is the ID for "The 500 Club"

        -- If the user has completed 500 workouts and the achievement hasn't been awarded
        IF completed_workouts >= 500 AND achievement_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 12, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_centurion` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE completed_workouts INT;
    DECLARE achievement_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Count the total number of finished workouts for the user
        SELECT COUNT(*) INTO completed_workouts
        FROM schedule_tbl
        WHERE username = NEW.username 
          AND status = 'finished';

        -- Check if the user already has the "Centurion" achievement
        SELECT COUNT(*) INTO achievement_count
        FROM user_achievements_tbl
        WHERE username = NEW.username 
          AND achievement_id = 11;  -- Assuming '11' is the ID for "Centurion"

        -- If the user has completed 100 workouts and the achievement hasn't been awarded
        IF completed_workouts >= 100 AND achievement_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 11, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_consistent_hustler` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE completed_workouts INT;
    DECLARE achievement_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Count the total number of finished workouts for the user
        SELECT COUNT(*) INTO completed_workouts
        FROM schedule_tbl
        WHERE username = NEW.username 
          AND status = 'finished';

        -- Check if the user already has the "Consistent Hustler" achievement
        SELECT COUNT(*) INTO achievement_count
        FROM user_achievements_tbl
        WHERE username = NEW.username 
          AND achievement_id = 9;  -- Assuming '9' is the ID for "Consistent Hustler"

        -- If the user has completed 20 workouts and the achievement hasn't been awarded
        IF completed_workouts >= 20 AND achievement_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 9, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_early_bird` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE user_username VARCHAR(255);
    DECLARE early_workouts INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Get the current event's username
        SET user_username = NEW.username;

        -- Count the number of workouts before 6 AM
        SELECT COUNT(*) INTO early_workouts
        FROM (
            SELECT * 
            FROM schedule_tbl
            WHERE username = NEW.username 
              AND status = 'finished' 
              AND TIME(start_time) < '06:00:00'
        ) AS early_workouts_count;

        -- Insert the achievement if the user has completed at least one early workout
        IF early_workouts > 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 21, NOW());  -- Assuming '21' is the achievement ID for "Early Bird"
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_endurance_challenger` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE workout_duration INT;
    DECLARE achievement_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Calculate the duration of the workout in minutes
        SET workout_duration = TIME_TO_SEC(TIMEDIFF(NEW.end_time, NEW.start_time)) / 60;

        -- Check if the user already has the "Endurance Challenger" achievement
        SELECT COUNT(*) INTO achievement_count
        FROM user_achievements_tbl
        WHERE username = NEW.username AND achievement_id = 16; -- Assuming '16' is the achievement ID for "Endurance Challenger"

        -- Insert the achievement if the workout duration is at least 60 minutes and hasn't been awarded yet
        IF workout_duration >= 60 AND achievement_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 16, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_first_step` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE user_username VARCHAR(255);
    DECLARE week_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Get the current event's username
        SET user_username = NEW.username;

        -- Check if the user has already unlocked the "First Step" achievement
        SELECT COUNT(*) INTO week_count
        FROM user_achievements_tbl
        WHERE username = NEW.username 
          AND achievement_id = 6;  -- Assuming '6' is the achievement ID for "First Step"

        -- Insert the achievement if not already inserted
        IF week_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 6, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_fitness_enthusiast` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE completed_workouts INT;
    DECLARE achievement_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Count the total number of finished workouts for the user
        SELECT COUNT(*) INTO completed_workouts
        FROM schedule_tbl
        WHERE username = NEW.username 
          AND status = 'finished';

        -- Check if the user already has the "Fitness Enthusiast" achievement
        SELECT COUNT(*) INTO achievement_count
        FROM user_achievements_tbl
        WHERE username = NEW.username 
          AND achievement_id = 10;  -- Assuming '10' is the ID for "Fitness Enthusiast"

        -- If the user has completed 50 workouts and the achievement hasn't been awarded
        IF completed_workouts >= 50 AND achievement_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 10, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_fitness_marathoner` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE workout_duration INT;
    DECLARE monthly_total_duration INT;
    DECLARE achievement_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Calculate the duration of the workout in minutes
        SET workout_duration = TIME_TO_SEC(TIMEDIFF(NEW.end_time, NEW.start_time)) / 60;

        -- Sum the total workout duration for the current month
        SELECT SUM(workout_duration) INTO monthly_total_duration
        FROM (
            SELECT TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 60 AS workout_duration
            FROM schedule_tbl
            WHERE username = NEW.username 
              AND status = 'finished' 
              AND MONTH(date) = MONTH(NEW.date) 
              AND YEAR(date) = YEAR(NEW.date)
        ) AS monthly_workouts;

        -- Check if the user already has the "Fitness Marathoner" achievement
        SELECT COUNT(*) INTO achievement_count
        FROM user_achievements_tbl
        WHERE username = NEW.username AND achievement_id = 18; -- Assuming '18' is the achievement ID for "Fitness Marathoner"

        -- Insert the achievement if the monthly total duration is at least 600 minutes (10 hours) and hasn't been awarded yet
        IF monthly_total_duration >= 600 AND achievement_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 18, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_half_hour_hero` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE workout_duration INT;
    DECLARE achievement_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Calculate the duration of the workout in minutes
        SET workout_duration = TIME_TO_SEC(TIMEDIFF(NEW.end_time, NEW.start_time)) / 60;

        -- Check if the user already has the "Half-hour Hero" achievement
        SELECT COUNT(*) INTO achievement_count
        FROM user_achievements_tbl
        WHERE username = NEW.username AND achievement_id = 15; -- Assuming '15' is the achievement ID for "Half-hour Hero"

        -- Insert the achievement if the workout duration is at least 30 minutes and hasn't been awarded yet
        IF workout_duration >= 30 AND achievement_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 15, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_half_year_hero` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE user_username VARCHAR(255);
    DECLARE week_start_date DATE;
    DECLARE week_count INT;

    -- Get the current event's username
    SET user_username = NEW.username;

    -- Start of the current week
    SET week_start_date = DATE_FORMAT(NEW.date, '%Y-%m-%d') - INTERVAL (DAYOFWEEK(NEW.date) - 1) DAY;

    -- Calculate the date 26 weeks ago
    SET week_count = DATE_FORMAT(DATE_SUB(NEW.date, INTERVAL 26 WEEK), '%Y-%m-%d');

    -- Count distinct weeks where user finished workouts
    SELECT COUNT(DISTINCT week_start_date) INTO week_count
    FROM schedule_tbl
    WHERE username = NEW.username 
      AND status = 'finished' 
      AND date >= week_count 
      AND date <= NEW.date;

    -- Insert the achievement only if user has finished workouts for 26 distinct weeks
    IF week_count >= 26 THEN
        -- Check if the achievement already exists for the user
        SELECT COUNT(*) INTO week_count
        FROM user_achievements_tbl
        WHERE username = NEW.username 
          AND achievement_id = 4;  -- Assuming '4' is the achievement ID for "Half Year Hero"

        -- Insert the achievement if not already inserted
        IF week_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 4, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_level_up` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE user_username VARCHAR(255);
    DECLARE completed_workouts INT;
    DECLARE achievement_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Get the current event's username
        SET user_username = NEW.username;

        -- Count the number of finished workouts
        SELECT COUNT(*) INTO completed_workouts
        FROM schedule_tbl
        WHERE username = NEW.username 
          AND status = 'finished';

        -- Insert the achievement if the user has completed at least 10 workouts
        IF completed_workouts >= 10 THEN
            -- Check if the achievement already exists for the user
            SELECT COUNT(*) INTO achievement_count
            FROM user_achievements_tbl
            WHERE username = NEW.username 
              AND achievement_id = 8;  -- Assuming '8' is the achievement ID for "Level Up"

            -- Insert the achievement if not already inserted
            IF achievement_count = 0 THEN
                INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
                VALUES (NEW.username, 8, NOW());
            END IF;
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_lucky_7_any_year` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE user_username VARCHAR(255);

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Get the current event's username
        SET user_username = NEW.username;

        -- Check if there's a workout logged on July 7
        IF DATE_FORMAT(NEW.date, '%m-%d') = '07-07' THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 23, NOW());  -- Assuming '23' is the achievement ID for "Lucky 7"
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_lunch_crunch` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE user_username VARCHAR(255);

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Get the current event's username
        SET user_username = NEW.username;

        -- Check if the workout starts at 12:34 PM
        IF TIME_FORMAT(NEW.start_time, '%H:%i') = '12:34' THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 24, NOW());  -- Assuming '24' is the achievement ID for "Lunch Crunch"
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_marathon_spirit` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE workout_duration INT;
    DECLARE achievement_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Calculate the duration of the workout in minutes
        SET workout_duration = TIME_TO_SEC(TIMEDIFF(NEW.end_time, NEW.start_time)) / 60;

        -- Check if the user already has the "Marathon Spirit" achievement
        SELECT COUNT(*) INTO achievement_count
        FROM user_achievements_tbl
        WHERE username = NEW.username AND achievement_id = 17; -- Assuming '17' is the achievement ID for "Marathon Spirit"

        -- Insert the achievement if the workout duration is at least 120 minutes and hasn't been awarded yet
        IF workout_duration >= 120 AND achievement_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 17, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_night_owl` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE user_username VARCHAR(255);
    DECLARE midnight_workouts INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Get the current event's username
        SET user_username = NEW.username;

        -- Count the number of workouts between midnight and 3 AM
        SELECT COUNT(*) INTO midnight_workouts
        FROM (
            SELECT * 
            FROM schedule_tbl
            WHERE username = NEW.username 
              AND status = 'finished' 
              AND TIME(start_time) BETWEEN '00:00:00' AND '03:00:00'
        ) AS midnight_workouts_count;

        -- Insert the achievement if the user has completed at least one midnight workout
        IF midnight_workouts > 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 20, NOW());  -- Assuming '20' is the achievement ID for "Night Owl"
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_on_a_roll` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE completed_workouts INT;
    DECLARE achievement_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Count the number of finished workouts for the user
        SELECT COUNT(*) INTO completed_workouts
        FROM schedule_tbl
        WHERE username = NEW.username AND status = 'finished';

        -- Check if the user already has the "On a Roll" achievement
        SELECT COUNT(*) INTO achievement_count
        FROM user_achievements_tbl
        WHERE username = NEW.username AND achievement_id = 7;  -- Assuming '7' is the achievement ID for "On a Roll"

        -- Insert the achievement if the user has completed at least 5 workouts and hasn't unlocked it yet
        IF completed_workouts >= 5 AND achievement_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 7, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_quick_starter` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE duration_minutes INT;
    DECLARE achievement_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Calculate workout duration in minutes
        SET duration_minutes = TIMESTAMPDIFF(MINUTE, NEW.start_time, NEW.end_time);

        -- Check if the user already has the "Quick Starter" achievement
        SELECT COUNT(*) INTO achievement_count
        FROM user_achievements_tbl
        WHERE username = NEW.username 
          AND achievement_id = 14;  -- Assuming '14' is the ID for "Quick Starter"

        -- If the workout duration is 10 minutes or more and the achievement hasn't been awarded
        IF duration_minutes >= 10 AND achievement_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 14, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_two_in_a_row_finished_once` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE user_username VARCHAR(255);
    DECLARE prev_date DATE;
    DECLARE current_count INT;

    -- Get the current event's username and status
    SET user_username = NEW.username;

    -- Fetch the previous day's event for the same user with status 'finished'
    SELECT MAX(date) INTO prev_date 
    FROM schedule_tbl
    WHERE username = NEW.username AND date = DATE_SUB(NEW.date, INTERVAL 1 DAY) AND status = 'finished';

    -- Check if there's a previous day's event with status 'finished'
    IF prev_date IS NOT NULL THEN
        -- Count consecutive finished workouts
        SELECT COUNT(*) INTO current_count
        FROM schedule_tbl
        WHERE username = NEW.username 
          AND status = 'finished' 
          AND date >= DATE_SUB(NEW.date, INTERVAL 1 DAY) 
          AND date <= NEW.date;

        -- Insert the achievement only if exactly 2 consecutive finished workouts and achievement is not yet inserted
        IF current_count = 2 THEN
            -- Check if the achievement already exists for the user
            SELECT COUNT(*) INTO current_count
            FROM user_achievements_tbl
            WHERE username = NEW.username 
              AND achievement_id = 2;  -- Assuming '2' is the achievement ID for "Two in a Row"

            -- Insert the achievement if not already inserted
            IF current_count = 0 THEN
                INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
                VALUES (NEW.username, 2, NOW());
            END IF;
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_weekend_warrior` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE user_username VARCHAR(255);
    DECLARE weekend_workouts INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Get the current event's username
        SET user_username = NEW.username;

        -- Count the number of workouts with a duration of 45 minutes or more for the weekend
        SELECT COUNT(*) INTO weekend_workouts
        FROM (
            SELECT * 
            FROM schedule_tbl
            WHERE username = NEW.username 
              AND status = 'finished' 
              AND DATE_FORMAT(date, '%Y-%m-%d') BETWEEN DATE_FORMAT(NEW.date, '%Y-%m-%d') AND DATE_ADD(NEW.date, INTERVAL 1 DAY) - INTERVAL 1 SECOND
              AND TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 60 >= 45
        ) AS weekend_long_workouts;

        -- Insert the achievement if the user has completed at least 3 long workouts
        IF weekend_workouts >= 3 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 19, NOW());  -- Assuming '19' is the achievement ID for "Weekend Warrior"
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_weekly_warrior` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE user_username VARCHAR(255);
    DECLARE week_start_date DATE;
    DECLARE weekly_count INT;

    -- Get the current event's username
    SET user_username = NEW.username;

    -- Start of the current week
    SET week_start_date = DATE_FORMAT(NEW.date, '%Y-%m-%d') - INTERVAL (DAYOFWEEK(NEW.date) - 1) DAY;

    -- Count workouts within the same week
    SELECT COUNT(*) INTO weekly_count
    FROM schedule_tbl
    WHERE username = NEW.username 
      AND status = 'finished' 
      AND date >= week_start_date 
      AND date <= NEW.date;

    -- Insert the achievement only if at least 5 workouts in a week and not already inserted
    IF weekly_count >= 5 THEN
        -- Check if the achievement already exists for the user
        SELECT COUNT(*) INTO weekly_count
        FROM user_achievements_tbl
        WHERE username = NEW.username 
          AND achievement_id = 3;  -- Assuming '3' is the achievement ID for "Weekly Warrior"

        -- Insert the achievement if not already inserted
        IF weekly_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 3, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_workout_legend` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE completed_workouts INT;
    DECLARE achievement_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Count the total number of finished workouts for the user
        SELECT COUNT(*) INTO completed_workouts
        FROM schedule_tbl
        WHERE username = NEW.username 
          AND status = 'finished';

        -- Check if the user already has the "Workout Legend" achievement
        SELECT COUNT(*) INTO achievement_count
        FROM user_achievements_tbl
        WHERE username = NEW.username 
          AND achievement_id = 13;  -- Assuming '13' is the ID for "Workout Legend"

        -- If the user has completed 1000 workouts and the achievement hasn't been awarded
        IF completed_workouts >= 1000 AND achievement_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 13, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_year_of_champion` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE user_username VARCHAR(255);
    DECLARE week_start_date DATE;
    DECLARE week_count INT;

    -- Get the current event's username
    SET user_username = NEW.username;

    -- Start of the current week
    SET week_start_date = DATE_FORMAT(NEW.date, '%Y-%m-%d') - INTERVAL (DAYOFWEEK(NEW.date) - 1) DAY;

    -- Calculate the date 52 weeks ago
    SET week_count = DATE_FORMAT(DATE_SUB(NEW.date, INTERVAL 52 WEEK), '%Y-%m-%d');

    -- Count distinct weeks where user finished workouts
    SELECT COUNT(DISTINCT week_start_date) INTO week_count
    FROM schedule_tbl
    WHERE username = NEW.username 
      AND status = 'finished' 
      AND date >= week_count 
      AND date <= NEW.date;

    -- Insert the achievement only if user has finished workouts for 52 distinct weeks
    IF week_count >= 52 THEN
        -- Check if the achievement already exists for the user
        SELECT COUNT(*) INTO week_count
        FROM user_achievements_tbl
        WHERE username = NEW.username 
          AND achievement_id = 5;  -- Assuming '5' is the achievement ID for "Year of the Champion"

        -- Insert the achievement if not already inserted
        IF week_count = 0 THEN
            INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
            VALUES (NEW.username, 5, NOW());
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `workout_playlist` AFTER UPDATE ON `schedule_tbl` FOR EACH ROW BEGIN
    DECLARE user_username VARCHAR(255);
    DECLARE unique_titles_count INT;

    -- Only trigger when status is set to 'finished'
    IF NEW.status = 'finished' THEN
        -- Get the current event's username
        SET user_username = NEW.username;

        -- Count workouts with unique titles
        SELECT COUNT(DISTINCT title) INTO unique_titles_count
        FROM schedule_tbl
        WHERE username = NEW.username 
          AND status = 'finished';

        -- Check if the achievement already exists for the user
        IF unique_titles_count >= 5 THEN
            IF NOT EXISTS (SELECT 1 FROM user_achievements_tbl WHERE username = NEW.username AND achievement_id = 22) THEN
                INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved)
                VALUES (NEW.username, 22, NOW());
            END IF;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users_tbl`
--

CREATE TABLE `users_tbl` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(150) NOT NULL,
  `EMAIL` varchar(150) NOT NULL,
  `PASSWORD` varchar(150) NOT NULL,
  `F_NAME` varchar(150) NOT NULL,
  `L_NAME` varchar(150) NOT NULL,
  `BIRTHDATE` date DEFAULT NULL,
  `AGE` int(11) DEFAULT NULL,
  `GENDER` varchar(20) DEFAULT NULL,
  `DATE_CREATED` timestamp NOT NULL DEFAULT current_timestamp(),
  `PROFILE_PICTURE` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_tbl`
--

INSERT INTO `users_tbl` (`ID`, `USERNAME`, `EMAIL`, `PASSWORD`, `F_NAME`, `L_NAME`, `BIRTHDATE`, `AGE`, `GENDER`, `DATE_CREATED`, `PROFILE_PICTURE`, `last_login`) VALUES
(1, 'User1', 'user1@gmail.com', '$2y$10$FsuEELISgSRGKnvZH3.3AO9LShQXYNK1/CKObN9YntGzG7o0Qfg92', '', '', NULL, NULL, NULL, '2025-06-22 08:06:46', '', '2025-06-22 16:06:52');

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements_tbl`
--

CREATE TABLE `user_achievements_tbl` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `achievement_id` int(11) NOT NULL,
  `date_achieved` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'unlocked'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements_tbl`
--
ALTER TABLE `achievements_tbl`
  ADD PRIMARY KEY (`achievement_id`);

--
-- Indexes for table `achievement_notifications_tbl`
--
ALTER TABLE `achievement_notifications_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_tbl`
--
ALTER TABLE `admin_tbl`
  ADD PRIMARY KEY (`A_ID`);

--
-- Indexes for table `fitness_entries`
--
ALTER TABLE `fitness_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications_tbl`
--
ALTER TABLE `notifications_tbl`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `schedule_tbl`
--
ALTER TABLE `schedule_tbl`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `users_tbl`
--
ALTER TABLE `users_tbl`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `user_achievements_tbl`
--
ALTER TABLE `user_achievements_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `achievement_id` (`achievement_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements_tbl`
--
ALTER TABLE `achievements_tbl`
  MODIFY `achievement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `achievement_notifications_tbl`
--
ALTER TABLE `achievement_notifications_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_tbl`
--
ALTER TABLE `admin_tbl`
  MODIFY `A_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fitness_entries`
--
ALTER TABLE `fitness_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications_tbl`
--
ALTER TABLE `notifications_tbl`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedule_tbl`
--
ALTER TABLE `schedule_tbl`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_tbl`
--
ALTER TABLE `users_tbl`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_achievements_tbl`
--
ALTER TABLE `user_achievements_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_achievements_tbl`
--
ALTER TABLE `user_achievements_tbl`
  ADD CONSTRAINT `user_achievements_tbl_ibfk_1` FOREIGN KEY (`achievement_id`) REFERENCES `achievements_tbl` (`achievement_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
