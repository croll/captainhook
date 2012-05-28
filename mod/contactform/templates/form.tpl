{extends tplextends('webpage/webpage_html5')}

{block name='webpage_head' append}
	{js file="/mod/cssjs/js/mootools.more.js"}
	{js file="/mod/cssjs/js/captainhook.js"}
	{js file="/mod/cssjs/js/messageclass.js"}
	{js file="/mod/cssjs/js/message.js"}
	{css file="/mod/cssjs/css/message.css"}
{/block}

{block name='webpage_body'}

	{if (empty($config) || empty($config.key))}
		<p class="alert alert-warn">{t d="contactform" m="You have to define your recapcha api key in the contact module interface, default link is /contact/admin."}
	{/if}

	{if (isset($error) && sizeof($error) > 0)}
		<p class="alert alert-warn">
			{foreach $error as $err}
				{$err}<br />
			{/foreach}
		</p>
	{/if}

	{form mod="contactform" file="templates/form.json"}
		<fieldset>
			<legend>{t d='contactform' m="Contact form"}</legend>
			<div class="control-group">
				<label class="control-label">{t d="contactform" m="Name"}</label>
				<div class="controls">
				{$formContact.name}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">{t d="contactform" m="Email"}</label>
				<div class="controls">
					{$formContact.email}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">{t d="contactform" m="Company"}</label>
				<div class="controls">
					{$formContact.company}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">{t d="contactform" m="Phone"}</label>
				<div class="controls">
					{$formContact.phone}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">{t d="contactform" m="Category"}</label>
				<div class="controls">
					<select name="category">
					{foreach $config.catmails as $cat=>$mails}
						<option value="{$cat}">{$cat}</option>
					{/foreach}
					</select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">{t d="contactform" m="Subject"}</label>
				<div class="controls">
					{$formContact.subject}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">{t d="contactform" m="Message"}</label>
				<div class="controls">
					{$formContact.message}
				</div>
			</div>
			<div>
				{$recaptcha}
			</div>
			<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k={$config.key}"></script>
			<div class="form-actions">
					{$formContact.submit}
			</div>
		</fieldset>
	{/form}
{/block}
