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
    document.forms.aliases_form.action.value = "select_domain";
    document.forms.aliases_form.domain_id.value = domain_id;
    document.forms.aliases_form.submit();
}

function SelectUser(user_id) {
    document.forms.aliases_form.action.value = "select_user";
    document.forms.aliases_form.user_id.value = user_id;
    document.forms.aliases_form.submit();
}

function NewAlias() {
    document.forms.aliases_form.action.value = "new_alias";
    document.forms.aliases_form.submit();
}

function InsertNewAlias() {
    document.forms.aliases_form.action.value = "insert_new_alias";
    document.forms.aliases_form.submit();
}

function EditAlias(alias_id) {
    document.forms.aliases_form.action.value = "edit_alias";
    document.forms.aliases_form.alias_id.value = alias_id;
    document.forms.aliases_form.submit();
}

function UpdateEditAlias(alias_id) {
    document.forms.aliases_form.action.value = "update_edit_alias";
    document.forms.aliases_form.alias_id.value = alias_id;
    document.forms.aliases_form.submit();
}

function DeleteAlias(alias_id) {
    document.forms.aliases_form.action.value = "delete_alias";
    document.forms.aliases_form.alias_id.value = alias_id;
    document.forms.aliases_form.submit();
}
