<div style="width: 100%; height: 300px; text-align: center; border: solid 1px black; background: #aaa">
	{hook mod='webpage' name='body'}

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


</div>
