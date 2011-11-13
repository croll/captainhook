{extends tplextends('webpage', 'webpage_main')}
{block name='webpage_body'}
{FieldForm hook_on_post='mod_exemple_person'}

	<div> Firstname:
	  {Field phpclass='\mod\field\Text' name='firstname' value=''}
      	     {FieldValidation regexp='^$' message='Ce champ est obligatoire'}
      	     {FieldValidation regexp='^[a-zA-Z]*$' message='Doit contenir uniquement des lettres'}
      	     {FieldValidation regexp='^.{2,30}$' message='Doit faire entre 2 et 30 caractères'}
	  {/Field}
	</div>

	<div> Lastname:
	  {Field phpclass='\mod\field\Text' name='lastname' value=''}
      	     {FieldValidation regexp='^$' message='Ce champ est obligatoire'}
      	     {FieldValidation regexp='^[a-zA-Z]*$' message='Doit contenir uniquement des lettres'}
      	     {FieldValidation regexp='^.{2,30}$' message='Doit faire entre 2 et 30 caractères'}
	  {/Field}
	</div>

	<div>
	  {Field phpclass='\mod\field\Submit' name='submit' value='Envoi la sauce !'}{/Field}
	</div>

{/FieldForm}
{/block}

