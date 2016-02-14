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

require_once ('./inc/domain.class.php');
require_once ('./inc/user.class.php');

function process_page($app, $action, &$var)
{
    $var['domain_count'] = Domain::count();
    $var['user_count'] = User::count();
    $var['domains'] = Domain::list_all();

    if (!isset($var['domain_id'])) {
        if (isset($_COOKIE['app/users/domain_id'])) {
            $var['domain_id'] = filter_var($_COOKIE['app/users/domain_id'], FILTER_SANITIZE_STRING);
            if (Domain::list_by_id($var['domain_id']) == null) {
                $var['domain_id'] = 0;
            }
        }
        else {
            $var['domain_id'] = 0;
        }
    }

    if ($var['domain_id'] != 0) {
        $var['users'] = User::list_by_domain_id($var['domain_id']);
        $var['active_domain'] = Domain::list_by_id($var['domain_id']);
    } else {
        $var['users'] = User::list_all();
    }

    switch ($app->action) {
      case 'select_domain':
        setcookie('app/users/domain_id', $var['domain_id']);
        $app->reload();
        break;
      case 'edit_user':
        $user = User::list_by_id($var['user_id']);
        $var['user_email'] = $user->get_email();
        $var['user_password'] = '';
        if ($user->is_admin()) {
            $var['user_admin'] = 'on';
        }
        else {
            unset($var['user_admin']);
        }
        break;
      case 'insert_new_user':
        $user = new User();
        $user->set_email($var['user_email']);
        $user->set_password($var['user_password']);
        $user->set_domain_id($var['domain_id']);
        $user->set_admin(isset($var['user_admin']));
        if ($user->insert()) {
            $app->reload();
        }
        else {
            $app->action = 'new_user';
        }
        break;
      case 'update_edit_user':
        $user = User::list_by_id($var['user_id']);
        $user->set_email($var['user_email']);
        $user->set_password($var['user_password']);
        $user->set_admin(isset($var['user_admin']));
        if ($user->update()) {
            $app->reload();
        }
        else {
            $app->action = 'edit_user';
        }
        break;
      case 'delete_user':
        $user = User::list_by_id($var['user_id']);
        if ($user->delete()) {
            $app->reload();
        }
        break;
    }
}
