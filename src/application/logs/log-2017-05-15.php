<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2017-05-15 13:53:35 --> Query error: Unknown column 'task.DateClose' in 'where clause' - Invalid query: DELETE FROM `assol_tasks`
WHERE task.DateClose < DATE_ADD(NOW(), INTERVAL -1 WEEK)
AND `task`.`State` = 2
ERROR - 2017-05-15 13:53:35 --> DELETE FROM `assol_tasks`
WHERE task.DateClose < DATE_ADD(NOW(), INTERVAL -1 WEEK)
AND `task`.`State` = 2
