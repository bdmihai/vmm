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

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

function process_page($app, $action, &$var)
{
    global $config;

    // the default date format is "Y-m-d H:i:s"
    $dateFormat = "Y-m-d H:i:s";
    // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
    $output = "[%datetime%]: %message%\n";
    // finally, create a formatter
    $formatter = new LineFormatter($output, $dateFormat);

    // create the login log
    $stream = new StreamHandler($config['logfile'], Logger::INFO);
    $stream->setFormatter($formatter);
    $logger = new Logger('login');
    $logger->pushHandler($stream);

    // get headers
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
                    $logger->addInfo("IP: ".$ip." Login success: ".$user->get_email());
                    $app->reload();
                }
                else {
                    $app->err->add($app->txt->tr('txt_login_no_access'));
                    $logger->addError("IP: ".$ip." Login failed: ".$user->get_email()." Reason: ".$app->txt->tr('txt_login_no_access'));
                }
            }
            else {
                $app->err->add($app->txt->tr('txt_login_wrong_password'));
                $logger->addError("IP: ".$ip." Login failed: ".$user->get_email()." Reason: ".$app->txt->tr('txt_login_wrong_password'));
            }
        }
        else {
            $app->err->add($app->txt->tr('txt_login_email_not_found'));
            $logger->addError("IP: ".$ip." Login failed: ".$var['email']." Reason: ".$app->txt->tr('txt_login_email_not_found'));
        }
    }
}
