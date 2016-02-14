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

class Domain
{
    private $id = 0;
    private $name = '';

    public function __construct() {
    }

    public function get_id() {
        return $this->id;
    }

    public function get_name() {
        return $this->name;
    }

    public function set_name($new_name) {
        global $app;
        $this->name = $app->db->escape($new_name);
    }

    public static function count() {
        global $app;
        $sql = 'SELECT count(*) FROM virtual_domains';

        return $app->db->select_first_value($sql);
    }

    public static function list_all() {
        global $app;
        $domain_list = array();
        $sql = 'SELECT * FROM virtual_domains ORDER BY name';
        $table = $app->db->select($sql);
        foreach ($table as $row) {
            $new_domain = new self();
            $new_domain->id = $row['id'];
            $new_domain->name = $row['name'];
            $domain_list[] = $new_domain;
        }

        return $domain_list;
    }

    public static function list_by_id($id) {
        global $app;
        $sql = "SELECT * FROM virtual_domains WHERE id=$id";
        $table = $app->db->select($sql);
        if (count($table) > 0) {
            $row = $table[0];
            $new_domain = new self();
            $new_domain->id = $row['id'];
            $new_domain->name = $row['name'];

            return $new_domain;
        }

        return;
    }

    public function insert() {
        global $app;

        // check for empty domain name
        if (empty($this->name)) {
            $app->err->add($app->txt->tr('txt_empty_domain_name'));

            return false;
        }

        // check for invalid domain name
        if (!$this->is_valid_domain_name($this->name)) {
            $app->err->add($app->txt->tr('txt_invalid_domain_name'));

            return false;
        }

        // check for existing domain name
        $sql = "SELECT count(*) FROM virtual_domains WHERE name='$this->name'";
        if ($app->db->select_first_value($sql) != 0) {
            $app->err->add($app->txt->tr('txt_exiting_domain_name'));

            return false;
        }

        // insert new domain
        $sql = "INSERT INTO virtual_domains (name) VALUES ('$this->name')";
        if (($this->id = $app->db->insert($sql)) == 0) {
            return false;
        }

        return true;
    }

    public function update() {
        global $app;

        // check for empty domain name
        if (empty($this->name)) {
            $app->err->add($app->txt->tr('txt_empty_domain_name'));

            return false;
        }

        // check for invalid domain name
        if (!$this->is_valid_domain_name($this->name)) {
            $app->err->add($app->txt->tr('txt_invalid_domain_name'));

            return false;
        }

        // check for existing domain name
        $sql = "SELECT count(*) FROM virtual_domains WHERE id=$this->id";
        if ($app->db->select_first_value($sql) == 0) {
            $app->err->add($app->txt->tr('txt_non_existing_domain_name'));

            return false;
        }

        // update domain
        $sql = "UPDATE virtual_domains SET name='$this->name' WHERE id=$this->id";
        if ($app->db->update($sql) == 0) {
            return false;
        }

        return true;
    }

    public function delete() {
        global $app;
        $sql = "DELETE FROM virtual_domains WHERE id=$this->id";
        if ($app->db->delete($sql) == 0) {
            return false;
        }

        return true;
    }

    private function is_valid_domain_name($domain_name) {
        return preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
              && preg_match('/^.{1,253}$/', $domain_name) //overall length check
              && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name); //length of each label
    }
}
