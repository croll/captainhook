<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\wkhtmltopdf;

class ModuleDefinition extends \core\ModuleDefinition {

	function __construct() {
		$this->description = 'Convert a HTML page to a PDF one, using wkhtmltopdf ';
		$this->name = 'wkhtmltopdf';
		$this->version = '0.1';
		$this->dependencies = array('smarty');
		parent::__construct();
	}

	function install() {
		parent::install();

    // do things here by default (after parent::install)
	}

	function uninstall() {
    // do things here by default (before parent::uninstall)

		parent::uninstall();
	}
}
