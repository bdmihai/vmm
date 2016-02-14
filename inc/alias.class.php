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

class Alias
{
    private $id = 0;
    private $domain_id = 0;
    private $source = '';
    private $destination = ''; // must exist + must be the same in virtual_users

  public function __construct()
  {
  }

    public function get_id()
    {
        return $this->id;
    }

    public function get_domain_id()
    {
        return $this->domain_id;
    }

    public function set_domain_id($new_domain_id)
    {
        $this->domain_id = $new_domain_id;
    }

    public function get_source()
    {
        return $this->source;
    }

    public function set_source($new_source)
    {
        global $app;
        $this->source = $app->db->escape($new_source);
    }

    public function get_destination()
    {
        return $this->destination;
    }

    public function set_destination($new_destination)
    {
        $this->destination = $new_destination;
    }

    public static function count()
    {
        global $app;
        $sql = 'SELECT count(*) FROM virtual_aliases';

        return $app->db->select_first_value($sql);
    }

    public static function list_all()
    {
        global $app;
        $aliases_list = array();
        $sql = 'SELECT * FROM virtual_aliases ORDER BY source';
        $table = $app->db->select($sql);
        foreach ($table as $row) {
            $new_alias = new self();
            $new_alias->id = $row['id'];
            $new_alias->domain_id = $row['domain_id'];
            $new_alias->source = $row['source'];
            $new_alias->destination = $row['destination'];
            $aliases_list[] = $new_alias;
        }

        return $aliases_list;
    }

    public static function list_by_id($id)
    {
        global $app;
        $sql = "SELECT * FROM virtual_aliases WHERE id=$id";
        $table = $app->db->select($sql);
        if (count($table) > 0) {
            $row = $table[0];
            $new_alias = new self();
            $new_alias->id = $row['id'];
            $new_alias->domain_id = $row['domain_id'];
            $new_alias->source = $row['source'];
            $new_alias->destination = $row['destination'];

            return $new_alias;
        }

        return;
    }

    public static function list_by_domain_id($id)
    {
        global $app;
        $aliases_list = array();
        $sql = "SELECT * FROM virtual_aliases WHERE domain_id=$id ORDER BY source";
        $table = $app->db->select($sql);
        foreach ($table as $row) {
            $new_alias = new self();
            $new_alias->id = $row['id'];
            $new_alias->domain_id = $row['domain_id'];
            $new_alias->source = $row['source'];
            $new_alias->destination = $row['destination'];
            $aliases_list[] = $new_alias;
        }

        return $aliases_list;
    }

    public static function list_by_destination($destination)
    {
        global $app;
        $aliases_list = array();
        $sql = "SELECT * FROM virtual_aliases WHERE destination='$destination' ORDER BY source";
        $table = $app->db->select($sql);
        foreach ($table as $row) {
            $new_alias = new self();
            $new_alias->id = $row['id'];
            $new_alias->domain_id = $row['domain_id'];
            $new_alias->source = $row['source'];
            $new_alias->destination = $row['destination'];
            $aliases_list[] = $new_alias;
        }

        return $aliases_list;
    }

    public function insert()
    {
        global $app;

      // check for empty source
      if (empty($this->source)) {
          $app->err->add($app->txt->tr('txt_empty_alias_source'));

          return false;
      }

      // check for empty destination
      if (empty($this->destination)) {
          $app->err->add($app->txt->tr('txt_empty_alias_destination'));

          return false;
      }

      // check for invalid source
      if (!filter_var($this->source, FILTER_VALIDATE_EMAIL) ||
          !$this->is_valid_user_email($this->source)) {
          $app->err->add($app->txt->tr('txt_invalid_alias_source'));

          return false;
      }

      // check for invalid destination
      if (!filter_var($this->destination, FILTER_VALIDATE_EMAIL) ||
          !$this->is_valid_user_email($this->destination)) {
          $app->err->add($app->txt->tr('txt_invalid_alias_destination'));

          return false;
      }

      // check for existing source/destination pair
      $sql = "SELECT count(*) FROM virtual_aliases WHERE source=$this->source AND destination=$this->destination";
        if ($app->db->select_first_value($sql) != 0) {
            $app->err->add($app->txt->tr('txt_existing_alias'));

            return false;
        }

      // check for existing domain
      $sql = "SELECT count(*) FROM virtual_domains WHERE id=$this->domain_id";
        if ($app->db->select_first_value($sql) == 0) {
            $app->err->add($app->txt->tr('txt_non_existing_alias_domain'));

            return false;
        }

      // insert alias
      $sql = "INSERT INTO virtual_aliases (domain_id,source,destination) VALUES ('$this->domain_id','$this->source','$this->destination')";
        if (($this->id = $app->db->insert($sql)) == 0) {
            return false;
        }

        return true;
    }

    public function update()
    {
        global $app;

      // check for empty source
      if (empty($this->source)) {
          $app->err->add($app->txt->tr('txt_empty_alias_source'));

          return false;
      }

      // check for invalid source
      if (!filter_var($this->source, FILTER_VALIDATE_EMAIL) ||
          !$this->is_valid_user_email($this->source)) {
          $app->err->add($app->txt->tr('txt_invalid_alias_source'));

          return false;
      }

      // check for existing source/destination pair
      $sql = "SELECT count(*) FROM virtual_aliases WHERE source=$this->source AND destination=$this->destination";
        if ($app->db->select_first_value($sql) != 0) {
            $app->err->add($app->txt->tr('txt_existing_alias'));

            return false;
        }

      // check for existing alias
      $sql = "SELECT count(*) FROM virtual_aliases WHERE id=$this->id";
        if ($app->db->select_first_value($sql) == 0) {
            $app->err->add($app->txt->tr('txt_non_existing_alias'));

            return false;
        }

      // update user
      $sql = "UPDATE virtual_aliases SET source='$this->source' WHERE id=$this->id";
        if ($app->db->update($sql) == 0) {
            return false;
        }

        return true;
    }

    public function delete()
    {
        global $app;
        $sql = "DELETE FROM virtual_aliases WHERE id=$this->id";
        if ($app->db->delete($sql) == 0) {
            return false;
        }

        return true;
    }

    private function is_valid_user_email($user_email)
    {
        return preg_match('/^[a-zA-Z0-9-@._]{3,30}$/', $user_email);
    }
}
