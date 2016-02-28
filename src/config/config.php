<?php
/*******************************************************************************
 * Copyright (C) 2010-2016 B.D. Mihai.
 *
 * This file is part of Virtual Mail Manager.
 *
 *   This program is free software: you can redistribute it and/or modify it
 *   under the terms of the GNU General Public License as published by the
 *   Free Software Foundation, either version 3 of the License, or (at your
 *   option) any later version.
 *
 *   Foobar is distributed in the hope that it will be useful, but WITHOUT
 *   ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 *   FITNESS FOR A PARTICULAR PURPOSE.  See the  GNU General Public License
 *   for more details.
 *
 *   You should have received a copy of the GNU General Public License along.
 *   If not, see <http://www.gnu.org/licenses/>.
 ******************************************************************************/

$config = array();

// database
$config['db_name']          = '';       // database name
$config['db_host']          = '';       // database server
$config['db_username']      = '';       // database user
$config['db_password']      = '';       // database password
// application
$config['debug']            = true;
$config['strict_variables'] = true;
$config['charset']          = 'utf-8';
$config['logfile']          = __DIR__.'/../../var/log/log.txt';
$config['cache']            = __DIR__.'/../../var/cache';
$config['templates']        = __DIR__.'/../templates';
