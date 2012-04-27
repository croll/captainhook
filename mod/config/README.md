# Config module for CaptainHook

This module allows developers to manage their module configuration.

Usage is quite simple, install the module and use set / get / delete methods.

## Add a config entry

The same method allows to add and edit an entry. 

#### Usage:

\mod\config\set($module, $name, $value, $user);

- $module (mandatory): can be the module id or module name. If the module does not exist or is not enabled in CaptainHook, a exception is thrown.
- $name (mandatory):  is the key of the entry, later you'll use it to retrieve the entry value. If the entry does not exists, an exception is thrown
- $value (mandatory): the value
- $user : the user name or the user id to be linked to the config entry.

## Retreive an entry

Get the entry value or the default value if no entry is set.

#### Usage:

\mod\config\get($module, $name, $defaultValue, $user);

- $module (mandatory): can be the module name or the module id. If the module does not exist or is not enabled in CaptainHook, an exception is thrown.
- $name (mandatory):  is the key of the entry, later you'll use it to retrieve the entry value. If the entry does not exists, an exception is thrown.
- $defaultValue: if no config entry is found, this value is returned. 
- $user : the user name or the user id who is linked to the config entry.

## Delete an entry

Simply delete an entry.

\mod\config\delete($module, $name, $user);

- $module (mandatory): can be the module name or the module id. If the module does not exist or is not enabled in CaptainHook, an exception is thrown.
- $name (mandatory):  is the key of the entry, later you'll use it to retrieve the entry value. If the entry does not exists, an exception is thrown.
- $user : the user name or the user id who is linked to the config entry.
