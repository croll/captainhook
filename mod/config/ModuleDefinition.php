<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-
/**
 * CaptainHook
 *
 * PHP Version 5
 *
 * @category  CaptainHook
 * @package   mod 
 * @author    Christophe Beveraggi (beve)
 * @copyright 2011-2012 CROLL (http://www.croll.fr)
 * @link      http://github.com/croll/captainhook
 * @license   LGPLv3
 *
 * CaptainHook is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * CaptainHook is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with CaptainHook.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace mod\config;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Allow to store modules configuration';
		$this->name = 'config';
		$this->version = '1.0';
		$this->dependencies = array('user');
		parent::__construct();
	}

	function install() {
		parent::install();
	}

	function uninstall() {
		parent::uninstall();
	}
}
