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

require_once ('./conf/config.php');
require_once ('./inc/user.class.php');

function process_page($app, $action, &$var)
{
    global $config;
    $date = date("Y-m-d H:i:s");
    $headers = getallheaders();
    if (isset($headers['X-Real-Ip'])) {
        $ip = $headers['X-Real-Ip'];
    }
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    if ($action == 'login') {
        $user = User::list_by_email($var['email']);
        if (!$user == null) {
            if ($user->verify_password($var['password'])) {
                if ($user->is_admin()) {
                    $_SESSION['user_id'] = $user->get_id();
                    $line = sprintf("[%s]: IP: %s Login success: %s\n", $date, $ip, $user->get_email());
                    if ($fp = @fopen($config['logfile'], 'a+')) {
                        fwrite($fp, $line);
                        fflush($fp);
                        fclose($fp);
                    }
                    $app->reload();
                }
                else {
                    $app->err->add($app->txt->tr('txt_login_no_access'));
                    $line = sprintf("[%s]: IP: %s Login failed: %s\n", $date, $ip, $app->txt->tr('txt_login_no_access'));
                    if ($fp = @fopen($config['logfile'], 'a+')) {
                        fwrite($fp, $line);
                        fflush($fp);
                        fclose($fp);
                    }
                }
            }
            else {
                $app->err->add($app->txt->tr('txt_login_wrong_password'));
                $line = sprintf("[%s]: IP: %s Login failed: %s\n", $date, $ip, $app->txt->tr('txt_login_wrong_password'));
                if ($fp = @fopen($config['logfile'], 'a+')) {
                    fwrite($fp, $line);
                    fflush($fp);
                    fclose($fp);
                }
            }
        }
        else {
            $app->err->add($app->txt->tr('txt_login_email_not_found'));
            $line = sprintf("[%s]: IP: %s Login failed: %s\n", $date, $ip, $app->txt->tr('txt_login_email_not_found'));
            if ($fp = @fopen($config['logfile'], 'a+')) {
                fwrite($fp, $line);
                fflush($fp);
                fclose($fp);
            }
        }
    }
}
