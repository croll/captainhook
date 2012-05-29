<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\contactform;

class Main {

	public static function hook_mod_contact_init($hookname, $userdata, $urlmatches) {
		require_once(dirname(__FILE__).'/ext/recaptcha/recaptchalib.php');
		$config = self::parseConfig();
		$page = new \mod\webpage\Main();
		$page->setLayout('contactform/form');
		$page->smarty->assign(array(
			'config' => $config,
			'recaptcha' => recaptcha_get_html($config['key'])
		));
		$page->display();
	}

	public static function hook_mod_contact_submit($hookname, $userdata, $urlmatches) {
		$error = array();
		$form = new \mod\form\Form(array('mod' => 'contactform', 'file' => 'templates/form.json'));
		$config = self::parseConfig();
		$page = new \mod\webpage\Main();
		$page->smarty->assign('config', $config);

		if (is_null($config) || !isset($config['privateKey'])) {
			$page->smarty->assign('configError', true);
		} else {
			$formFields = $form->getFieldValues();
			// HACK: please help me making evolutions in form module
			$formFields['category'] = $_POST['category'];
			require_once(dirname(__FILE__).'/ext/recaptcha/recaptchalib.php');

			$resp = recaptcha_check_answer(
				$config['privateKey'],
				$_SERVER["REMOTE_ADDR"],
				$_POST["recaptcha_challenge_field"],
				$_POST["recaptcha_response_field"]
			);

			if (!$resp->is_valid) {
				$error[] = \mod\lang\Main::ch_t('contactform', 'Captcha is invalid');
			}

			if (!$form->validate()) {
				$error[] = $form->getValidationErrors();
			}

			if (sizeof($error) == 0) {
				if (self::sendEmail($formFields)) {
					$page->smarty->assign('mailsent', true);
				} else {
					$page->smarty->assign('mailsent', false);
				}
				$page->setLayout('contactform/mail_sent');
			} else {
				$page->smarty->assign(array(
					'fields' => $formFields,
					'error' => $error,
					'recaptcha' => recaptcha_get_html($config['key'])
				));
				$page->setLayout('contactform/form');
			}
		}
		$page->display();
	}

	public static function hook_mod_contact_admin($hookname, $userdata, $urlmatches) {
		if (!\mod\user\Main::userHasRight('Configure contactform module'))
			die ('You are not allowed to configure this module');
		$page = new \mod\webpage\Main();
		$page->setLayout('contactform/admin');
		$page->smarty->assign('config', self::parseConfig());
		$page->display();
	}

	// Yes it's ugly, but things need to be done.
	// Rewrite it if you have some time.
	public static function parseConfig() {
		$config = json_decode(\mod\config\Main::get('contactform', 'config', null), true);
		if (isset($config['categories']) && !empty($config['categories'])) {
			$config['catmails'] = array();
			foreach($config['categories'] as $name=>$mails) {
				foreach(preg_split('/\|/', $name) as $n) {
					$tmp = preg_split('/:/', $n);
					if (sizeof($tmp) == 1) {
						foreach($mails as $m) {
							$config['catmails'][$tmp[0]][] = $m;
						}
					} else {
						if ($tmp[0] == \mod\lang\Main::getCurrentLang()) {
							foreach($mails as $m) {
								$config['catmails'][$tmp[1]][] = $m;
							}
						}
					}
				}
			}
		}
		return $config;
	}

	public static function sendEmail($formFields) {
		$config = self::parseConfig();
		$body = '';
		foreach($formFields as $field=>$value) {
			if ($field != 'submit' && !empty($value)) {
				$body.= $field.': '.$value."\n";
			}
		}
		if (!isset($config['mailSubject']) && empty($config['mailSubject'])) {
			throw new \Exception("Unable to send mail, mail subject is not defined");
		}
		if (!isset($config['catmails'][$formFields['category']]) || sizeof($config['catmails'][$formFields['category']]) == 0) {
			throw new \Exception("Unable to send mail, category $formFields[category] is undefined or have no email address");
		}
		$headers = (isset($config['sender']) && !empty($config['sender'])) ? "From: ".$config['sender']."\r\nX-Mailer: php" : "";
		foreach($config['catmails'][$formFields['category']] as $mail) {
			$ret = mail($mail, $config['mailSubject'], $body, $headers);
		}
		return $ret;
	}

}
