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
 
function process_page($app, $action, &$var) {
    $var['domain_count'] = Domain::count();
    $var['user_count'] = User::count();
    $var['domains'] = Domain::list_all();

    // perform requested actions
    switch ($app->action) {
      case 'edit_domain':
        $var['domain_name'] = Domain::list_by_id($var['domain_id'])->get_name();
        break;
      case 'insert_new_domain':
        $domain = new Domain();
        $domain->set_name($var['domain_name']);
        if ($domain->insert()) {
            $app->reload();
        } else {
            $app->action = 'new_domain';
        }
        break;
      case 'update_edit_domain':
        $domain = Domain::list_by_id($var['domain_id']);
        $domain->set_name($var['domain_name']);
        if ($domain->update()) {
            $app->reload();
        } else {
            $app->action = 'edit_domain';
        }
        break;
      case 'delete_domain':
        $domain = Domain::list_by_id($var['domain_id']);
        if ($domain->delete()) {
            $app->reload();
        }
        break;
    }
}
