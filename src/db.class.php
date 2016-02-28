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

class Db
{
    private $conn = null;
    private $err = null;

    public function __construct($err, $host, $username, $password, $name) {
        $this->err = $err;
        $this->conn = new mysqli($host, $username, $password, $name);
        if ($this->conn->connect_error) {
            $this->err->add($this->conn->connect_error);
            die('Connection failed: '.$this->conn->connect_error);
        }
        $this->conn->query("SET NAMES 'UTF8'");
    }

    public function __destruct() {
        $this->conn->close();
    }

    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }

    public function select_first_value($sql)
    {
        $result = $this->conn->query($sql);
        if ($result->field_count > 0 && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            return reset($row);
        }

        return;
    }

    public function select($sql)
    {
        $table = array();
        $result = $this->conn->query($sql);
        if ($result->field_count > 0 && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $table[] = $row;
            }
        }

        return $table;
    }

    public function insert($sql)
    {
        if ($this->conn->query($sql) === true) {
            return $this->conn->insert_id;
        } else {
            $this->err->add($this->conn->error);

            return 0;
        }
    }

    public function update($sql)
    {
        if ($this->conn->query($sql) === true) {
            return 1;
        } else {
            $this->err->add($this->conn->error);

            return 0;
        }
    }

    public function delete($sql)
    {
        if ($this->conn->query($sql) === true) {
            return 1;
        } else {
            $this->err->add($this->conn->error);

            return 0;
        }
    }
}
