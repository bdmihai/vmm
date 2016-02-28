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
 
function SelectDomain(domain_id) {
    document.forms.users_form.action.value = "select_domain";
    document.forms.users_form.domain_id.value = domain_id;
    document.forms.users_form.submit();
}

function NewUser() {
    document.forms.users_form.action.value = "new_user";
    document.forms.users_form.submit();
}

function InsertNewUser() {
    document.forms.users_form.action.value = "insert_new_user";
    document.forms.users_form.submit();
}

function EditUser(user_id) {
    document.forms.users_form.action.value = "edit_user";
    document.forms.users_form.user_id.value = user_id;
    document.forms.users_form.submit();
}

function UpdateEditUser(user_id) {
    document.forms.users_form.action.value = "update_edit_user";
    document.forms.users_form.user_id.value = user_id;
    document.forms.users_form.submit();
}

function DeleteUser(user_id) {
    document.forms.users_form.action.value = "delete_user";
    document.forms.users_form.user_id.value = user_id;
    document.forms.users_form.submit();
}
