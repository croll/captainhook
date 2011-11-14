<div> Firstname:
  {Field phpclass='\mod\field\Text' name='firstname' value=''}
     {FieldValidation regexp='/^$/' message='Ce champ est obligatoire' stop=1}
     {FieldValidation iregexp='/^[a-zA-Z]*$/' message='Doit contenir uniquement des lettres'}
     {FieldValidation iregexp='/^.{2,30}$/' message='Doit faire entre 2 et 30 caractères'}
  {/Field}
</div>

<div> Lastname:
  {Field phpclass='\mod\field\Text' name='lastname' value=''}
     {FieldValidation regexp='/^$/' message='Ce champ est obligatoire' stop=1}
     {FieldValidation iregexp='/^[a-zA-Z]*$/' message='Doit contenir uniquement des lettres'}
     {FieldValidation iregexp='/^.{2,30}$/' message='Doit faire entre 2 et 30 caractères'}
  {/Field}
</div>

<div>
  {Field phpclass='\mod\field\Submit' name='submit' value='Envoi la sauce !'}{/Field}
</div>
