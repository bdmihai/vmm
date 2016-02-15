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

require_once ('./conf/config.php');
require_once ('./inc/error.class.php');
require_once ('./inc/db.class.php');
require_once ('./inc/txt.class.php');
require_once ('./inc/user.class.php');
require_once ('./lib/Twig/Autoloader.php');

class App
{
    public $err = null;
    public $db = null;
    public $txt = null;
    public $page = '';
    public $action = '';
    public $var = array();
    public $status = '';
    public $navigation = array();

    public function __construct() {
    }

    public function init() {
        global $config;

        // error reporting
        if ($config['debug'] == false) {
            error_reporting(E_ALL & ~E_NOTICE  & ~E_WARNING & ~E_DEPRECATED);
        }

        // initialize the error handler
        $this->err = new Error();

        // initialize the database handler
        $this->db = new Db($this->err, $config['db_host'], $config['db_username'], $config['db_password'], $config['db_name']);

        // initialize the translation handler
        $this->txt = new Txt($this->db);

        // initialize twig framework
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem(array($config['templates']));
        $this->twig = new Twig_Environment($loader, $config);

        // possible navigation
        $this->navigation = array('info', 'domains', 'users', 'aliases');

        // start page
        $this->page = 'login';
    }

    public function process()
    {
        global $config;

        // check if there is data to be processed and figure out what page to display
        if (empty($_POST)) {
            // in case of empty post just refresh the previous page
            if (isset($_COOKIE['page'])) {
                $this->page = filter_var($_COOKIE['page'], FILTER_SANITIZE_STRING);
            }
        } else {
            // check if there is a page defined
            if (isset($_POST['page'])) {
                $this->page = filter_var($_POST['page'], FILTER_SANITIZE_STRING);

                // check if there is a action defined
                if (isset($_POST['action'])) {
                    $this->action = filter_var($_POST['action'], FILTER_SANITIZE_STRING);
                }

                // get all variables from the post
                foreach ($_POST as $key => $value) {
                    $this->var[$key] = filter_var($value, FILTER_SANITIZE_STRING);
                }
            } else {
                // in case no page is defined we need to show a error
                $this->err->add($this->txt->tr('txt_no_page'));
                $this->page = 'error';

                return;
            }
        }

        // process language change
        $language = 'en';
        if (isset($this->var['language']) && $this->action == 'change_language') {
            $language = $this->var['language'];
            setcookie('language', $language);
            $this->reload();
        } else {
            if (isset($_COOKIE['language'])) {
                $language = filter_var($_COOKIE['language'], FILTER_SANITIZE_STRING);
            }
        }
        $this->txt->set_language($language);

        // check authentication
        if (!isset($_SESSION['user_id'])) {
            $this->page = 'login';
            $this->status = $this->txt->tr('txt_version').' '.$config['version'];
        } else {
            if ($this->page == 'login') {
                $this->page = 'info';
            }
            else if ($this->page == 'logout') {
                session_unset();
                session_destroy();
                $this->reload();
            }
            $this->status = $this->txt->tr('txt_login_as').' '.User::list_by_id($_SESSION['user_id'])->get_email();
        }

        // process navigation
        if ($this->action == 'change_page') {
            setcookie('page', $this->page);
            $this->reload();
        }

        // distribute based on required page
        include './inc/'.$this->page.'.php';
        process_page($this, $this->action, $this->var);
    }

    public function render() {
        echo $this->twig->render($this->page.'.html', array('app' => $this, 'var' => $this->var));
    }

    public function reload() {
        header('Location: index.php');
        exit;
    }
}
