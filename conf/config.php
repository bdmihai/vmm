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

// version
$config['version']          = '1.1';
// database
$config['db_name']          = 'mailserver';    // database name
$config['db_host']          = '127.0.0.1';     // database server
$config['db_username']      = 'mailuser';      // database user
$config['db_password']      = 'mailuser';      // database password
// application
$config['debug']            = true;
$config['strict_variables'] = true;
$config['charset']          = 'utf-8';
$config['cache']            = './cache';
$config['templates']        = './templates';
$config['logfile']          = './log/log.txt';
