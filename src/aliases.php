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

function process_page($app, $action, &$var)
{
    $var['domain_count'] = Domain::count();
    $var['user_count'] = User::count();
    $var['domains'] = Domain::list_all();

    if (!isset($var['domain_id'])) {
        if (isset($_COOKIE['app/aliases/domain_id'])) {
            $var['domain_id'] = filter_var($_COOKIE['app/aliases/domain_id'], FILTER_SANITIZE_STRING);
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

    if (!isset($var['user_id'])) {
        if (isset($_COOKIE['app/aliases/user_id'])) {
            $var['user_id'] = filter_var($_COOKIE['app/aliases/user_id'], FILTER_SANITIZE_STRING);
            if (User::list_by_id($var['user_id']) == null) {
                $var['user_id'] = 0;
            }
        }
        else {
            $var['user_id'] = 0;
        }
    }

    if ($var['user_id'] != 0) {
        $var['active_user'] = User::list_by_id($var['user_id']);
        $var['aliases'] = Alias::list_by_destination($var['active_user']->get_email());
    } else {
        if ($var['domain_id'] != 0) {
            $var['aliases'] = Alias::list_by_domain_id($var['domain_id']);
        }
        else {
            $var['aliases'] = Alias::list_all();
        }
    }

    switch ($app->action) {
      case 'select_domain':
        setcookie('app/aliases/domain_id', $var['domain_id']);
        setcookie('app/aliases/user_id', null);
        $app->reload();
        break;
      case 'select_user':
        setcookie('app/aliases/user_id', $var['user_id']);
        $app->reload();
        break;
      case 'edit_alias':
        $var['alias_source'] = Alias::list_by_id($var['alias_id'])->get_source();
        break;
      case 'insert_new_alias':
        $alias = new Alias();
        $alias->set_destination($var['active_user']->get_email());
        $alias->set_domain_id($var['domain_id']);
        $alias->set_source($var['alias_source']);
        if ($alias->insert()) {
            $app->reload();
        }
        else {
            $app->action = 'new_alias';
        }
        break;
      case 'update_edit_alias':
        $alias = Alias::list_by_id($var['alias_id']);
        $alias->set_source($var['alias_source']);
        if ($alias->update()) {
            $app->reload();
        }
        else {
            $app->action = 'edit_alias';
        }
        break;
      case 'delete_alias':
        $alias = Alias::list_by_id($var['alias_id']);
        if ($alias->delete()) {
            $app->reload();
        }
        break;
    }
}
