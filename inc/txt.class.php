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
 
class Txt
{
    private $language = 'txt';
    private $db = null;
    private $all = array('en', 'de');

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get_languages()
    {
        return $this->all;
    }

    public function get_language()
    {
        return $this->language;
    }

    public function set_language($new_language)
    {
        $this->language = $new_language;
    }

  /**
   * Translate the text with the given identifier. The translation will consider
   * the selectd language (used directly as the column identifier).
   */
  public function tr($text)
  {
      $sql = "SELECT $this->language FROM virtual_txt WHERE txt='$text'";
      $res = $this->db->select_first_value($sql);
      if ($res == null || empty($res)) {
          return $text;
      }

      return $res;
  }
}
