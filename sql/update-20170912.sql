DROP TABLE IF EXISTS lesson_level;
CREATE TABLE `lesson_level` (
  `lesson_id` INT(11) NOT NULL,
  `level_id`  INT(11) NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
ALTER TABLE `lesson_level`
  ADD PRIMARY KEY (`lesson_id`, `level_id`),
  ADD KEY `IDX_D00C1894CDF80196` (`lesson_id`),
  ADD KEY `IDX_D00C18945FB14BA7` (`level_id`);
ALTER TABLE `lesson_level`
  ADD CONSTRAINT `FK_D00C18945FB14BA7` FOREIGN KEY (`level_id`) REFERENCES `level` (`id`)
  ON DELETE CASCADE,
  ADD CONSTRAINT `FK_D00C1894CDF80196` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`id`)
  ON DELETE CASCADE;
INSERT INTO lesson_level (lesson_id, level_id)
  SELECT
    id,
    level_id
  FROM lesson;
