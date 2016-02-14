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

require_once('./lib/passwordLibClass.php');

class User
{
    private $id = 0;
    private $domain_id = 0;
    private $email = '';
    private $password = '';
    private $master = false;
    private $domain_master = array();
    private $is_admin = false;

    public function __construct() {
    }

    public function get_id() {
        return $this->id;
    }

    public function get_domain_id() {
        return $this->domain_id;
    }

    public function set_domain_id($new_domain_id) {
        $this->domain_id = $new_domain_id;
    }

    public function get_email() {
        return $this->email;
    }

    public function set_email($new_email) {
        global $app;
        $this->email = $app->db->escape($new_email);
    }

    public function verify_password($password_to_verify) {
        $hash  = explode("{SHA256-CRYPT}", $this->password);
        $verified = false;
        if(count($hash) == 2) {
            $verified = (crypt($password_to_verify, $hash[1]) === $hash[1]);
        }
        else if(count($hash) == 1) {
            $verified = (crypt($password_to_verify, $hash[0]) === $hash[0]);
        }
        return $verified;
    }

    public function is_admin() {
        return $this->is_admin;
    }

    public function set_admin($new_admin) {
        $this->is_admin = $new_admin;
    }

    public function set_password($new_password) {
        global $app;
        $this->password = $app->db->escape($new_password);
    }

    public static function count() {
        global $app;
        $sql = 'SELECT count(*) FROM virtual_users';

        return $app->db->select_first_value($sql);
    }

    public static function list_all() {
        global $app;
        $user_list = array();
        $sql = 'SELECT * FROM virtual_users ORDER BY email';
        $table = $app->db->select($sql);
        foreach ($table as $row) {
            $new_user = new self();
            $new_user->id = $row['id'];
            $new_user->email = $row['email'];
            $new_user->password = $row['password'];
            $new_user->domain_id = $row['domain_id'];

            $sql = "SELECT count(*) FROM virtual_admins WHERE user_id=$new_user->id";
            $new_user->is_admin = ($app->db->select_first_value($sql) > 0);

            $user_list[] = $new_user;
        }

        return $user_list;
    }

    public static function list_by_id($id) {
        global $app;
        $sql = "SELECT * FROM virtual_users WHERE id=$id";
        $table = $app->db->select($sql);
        if (count($table) > 0) {
            $row = $table[0];
            $new_user = new self();
            $new_user->id = $row['id'];
            $new_user->email = $row['email'];
            $new_user->password = $row['password'];
            $new_user->domain_id = $row['domain_id'];

            $sql = "SELECT count(*) FROM virtual_admins WHERE user_id=$new_user->id";
            $new_user->is_admin = ($app->db->select_first_value($sql) > 0);

            return $new_user;
        }

        return null;
    }

    public static function list_by_email($email) {
        global $app;
        $sql = "SELECT * FROM virtual_users WHERE email='$email'";
        $table = $app->db->select($sql);
        if (count($table) > 0) {
            $row = $table[0];
            $new_user = new self();
            $new_user->id = $row['id'];
            $new_user->email = $row['email'];
            $new_user->password = $row['password'];
            $new_user->domain_id = $row['domain_id'];

            $sql = "SELECT count(*) FROM virtual_admins WHERE user_id=$new_user->id";
            $new_user->is_admin = ($app->db->select_first_value($sql) > 0);

            return $new_user;
        }

        return null;
    }

    public static function list_by_domain_id($id) {
        global $app;
        $user_list = array();
        $sql = "SELECT * FROM virtual_users WHERE domain_id=$id ORDER BY email";
        $table = $app->db->select($sql);
        foreach ($table as $row) {
            $new_user = new self();
            $new_user->id = $row['id'];
            $new_user->email = $row['email'];
            $new_user->password = $row['password'];
            $new_user->domain_id = $row['domain_id'];

            $sql = "SELECT count(*) FROM virtual_admins WHERE user_id=$new_user->id";
            $new_user->is_admin = ($app->db->select_first_value($sql) > 0);

            $user_list[] = $new_user;
        }

        return $user_list;
    }

    public function insert() {
        global $app;

        // check for empty user email
        if (empty($this->email)) {
            $app->err->add($app->txt->tr('txt_empty_user_email'));

            return false;
        }

        // check for empty user password
        if (empty($this->password)) {
            $app->err->add($app->txt->tr('txt_empty_user_password'));

            return false;
        }

        // check for invalid user email
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL) ||
            !$this->is_valid_user_email($this->email)) {
            $app->err->add($app->txt->tr('txt_invalid_user_email'));

            return false;
        }

        // check for invalid user password
        if (!$this->is_valid_user_password($this->password)) {
            $app->err->add($app->txt->tr('txt_invalid_user_password'));

            return false;
        }

        // check for existing domain
        $sql = "SELECT count(*) FROM virtual_domains WHERE id=$this->domain_id";
        if ($app->db->select_first_value($sql) == 0) {
            $app->err->add($app->txt->tr('txt_non_existing_user_domain'));

            return false;
        }

        // check for existing user email
        $sql = "SELECT count(*) FROM virtual_users WHERE email=$this->email";
        if ($app->db->select_first_value($sql) != 0) {
            $app->err->add($app->txt->tr('txt_existing_user_email'));

            return false;
        }

        // encrypt
        $crypt = NEW Antnee\PhpPasswordLib\PhpPasswordLib;
        $crypt->setAlgorithm(PASSWORD_SHA256);
        $this->password = $crypt->generateCryptPassword($this->password, array('cost' => 10));

        // insert user
        $sql = "INSERT INTO virtual_users (email,password,domain_id) VALUES ('$this->email','$this->password','$this->domain_id')";
        if (($this->id = $app->db->insert($sql)) == 0) {
            return false;
        }

        // set administrator
        $sql = "DELETE FROM virtual_admins WHERE user_id=$this->id";
        $app->db->delete($sql);
        if ($this->is_admin) {
            $sql = "INSERT INTO virtual_admins (user_id) VALUES ($this->id)";
            $app->db->insert($sql);
        }

        return true;
    }

    public function update() {
        global $app;

        // check for empty user email
        if (empty($this->email)) {
            $app->err->add($app->txt->tr('txt_empty_user_email'));

            return false;
        }

        // check for invalid user email
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL) ||
            !$this->is_valid_user_email($this->email)) {
            $app->err->add($app->txt->tr('txt_invalid_user_email'));

            return false;
        }

        // check for invalid user password
        if (!empty($this->password)) {
            if (!$this->is_valid_user_password($this->password)) {
                $app->err->add($app->txt->tr('txt_invalid_user_password'));

                return false;
            }
        }

        // check for existing user email
        $sql = "SELECT count(*) FROM virtual_users WHERE id=$this->id";
        if ($app->db->select_first_value($sql) == 0) {
            $app->err->add($app->txt->tr('txt_non_existing_user_email'));

            return false;
        }

        // encrypt
        if (!empty($this->password)) {
            $crypt = NEW Antnee\PhpPasswordLib\PhpPasswordLib;
            $crypt->setAlgorithm(PASSWORD_SHA256);
            $this->password = $crypt->generateCryptPassword($this->password, array('cost' => 10));
        }

        // update user
        if (!empty($this->password)) {
            $sql = "UPDATE virtual_users SET email='$this->email',password='$this->password' WHERE id=$this->id";
        }
        else {
            $sql = "UPDATE virtual_users SET email='$this->email' WHERE id=$this->id";
        }
        if ($app->db->update($sql) == 0) {
            return false;
        }

        // set administrator
        $sql = "DELETE FROM virtual_admins WHERE user_id=$this->id";
        $app->db->delete($sql);
        if ($this->is_admin) {
            $sql = "INSERT INTO virtual_admins (user_id) VALUES ($this->id)";
            $app->db->insert($sql);
        }

        return true;
    }

    public function delete() {
        global $app;
        $sql = "DELETE FROM virtual_users WHERE id=$this->id";
        if ($app->db->delete($sql) == 0) {
            return false;
        }

        return true;
    }

    private function is_valid_user_email($user_email) {
        return preg_match('/^[a-zA-Z0-9-@._]{3,30}$/', $user_email);
    }

    private function is_valid_user_password($user_password) {
        return preg_match('/^[a-zA-Z0-9@$]{8,15}$/', $user_password);
    }
}
