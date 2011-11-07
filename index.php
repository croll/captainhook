<?php
/** 
 * CaptainHook is designed by hackers for hackers for quickly and effiency build
 * web applications.
 *
 * CaptainHook allows to build web applications usking hooks and tricks. 
 * The general purpose is to be able to code quickly. Reuse, extend 
 * but don't fork others modules, you can do it in the right way without fatigue. 
 * Heavily based on Hooks, the Captain is able to undestand ship's boy requests and
 * perform the right actions.
 *
 * @author: Christophe Beveraggi (beve) and Nicolas Dimitrijevich (niclone)
 * @link: http://github.com/croll/captainhook
 * @license: LGPLv3
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

list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;

require_once(dirname(__FILE__).'/core/Core.php');
\core\Core::init();

list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5);

echo "RAM: ".\core\Tools::getMemoryUsage()." ; elapsed: ".($elapsed_time * 1000.0)."ms ; class count: ".$class_autoload_count;
