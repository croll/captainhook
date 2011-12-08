{FieldValidator name='user_empty' regexp='/^$/' message='Ce champ est obligatoire'}
{FieldValidator name='user_length' iregexp='/^.{2,30}$/' message='Le texte doit faire de 2 et 30 caractères'}

<div>
	<div>
		{Field phpclass='\mod\field\Text' title='login' name='login' validators='user_empty,user_length'}
	</div>
	<div>
		{Field phpclass='\mod\field\Password' title='password' name='password' validators='user_empty,user_length'}
	</div>
</div>
<div>
	{Field phpclass='\mod\field\Submit' name='submit' value='Identification'}
</div>

<script>
window.addEvent('domready', function(){
  document.id('user_loginform').getElements('[type=text], [type=password]').each(function(el){
    new OverText(el, {
			poll: true
		});
  });
});
</script>
