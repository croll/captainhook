{FieldValidator name='site_test_empty' regexp='/^$/' message='Ce champ est obligatoire'}
{FieldValidator name='site_test_allowedchars' iregexp='/^[a-zA-Z]*$/' message='Doit contenir uniquement des lettres'}
{FieldValidator name='site_test_length' iregexp='/^.{2,30}$/' message='Doit faire entre 2 et 30 caractères'}

<div> Firstname:
  {Field phpclass='\mod\field\Text' name='firstname' sqltable='ch_sitetest_person' value='' validators='site_test_empty,site_test_allowedchars,site_test_length'}
</div>

<div> Lastname:
  {Field phpclass='\mod\field\Text' name='lastname' sqltable='ch_sitetest_person' value=''  validators='site_test_empty,site_test_allowedchars,site_test_length'}
</div>

<div>
  {Field phpclass='\mod\field\Submit' name='submit' value='Envoi la sauce !'}
</div>
