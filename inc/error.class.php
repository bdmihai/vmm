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
 
class Error
{
    private $list = array();

    public function add($new_error)
    {
        return $this->list[] = $new_error;
    }

    public function clear()
    {
        unset($this->list);
        $this->list = array();
    }

    public function has_errors()
    {
        return count($this->list) > 0;
    }

    public function get_errors()
    {
        return $this->list;
    }
}
