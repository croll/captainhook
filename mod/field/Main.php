<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\field;

class Main {

	private static $curform=null;
	private static $curfield=null;

  public static function smartyBlock_Field($params, $content, $smarty, &$repeat) {
		$fieldform = $smarty->tpl_vars['fieldform']->value;
		if ($content === NULL) { // open tag
			$classname=$params['phpclass'];
			$field = new $classname($params['name'], $params['value']);
			$fieldform->curfield[]=$field;
			$fieldform->addField($field);
		} else { // close tag
			$field=array_pop($fieldform->curfield);
			return $field->render();
		}
  }

  public static function smartyFunction_FieldValidation($params, $template) {
		$fieldform = $template->smarty->tpl_vars['fieldform']->value;
		if (count($fieldform->curfield) == 0) throw new \Exception("FieldVerification must be inside a Field block");
		$field = $fieldform->curfield[count($fieldform->curfield)-1];
		if (isset($params['regexp'])) {
			$regexp=$params['regexp'];
			$inverted=false;
		} else if (isset($params['iregexp'])) {
			$regexp=$params['iregexp'];
			$inverted=true;
		} else throw new \Exception("FieldVerification must have at least regexp nor iregexp argument");
		$field->addVerification(new Verification($regexp, $params['message'], $inverted));
  }

	public static function hook_field_post($hookname, $userdata, $urlparams) {
		\core\Hook::call('field_post_'.$urlparams[1], $_POST);
	}

}

