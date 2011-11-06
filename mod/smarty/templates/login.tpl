<link type="text/css" rel="stylesheet" media="all" href="css/style_login.css" ></link>
<script language="javascript">
{literal}
window.addEvent('domready', function(){

  var formulaire = document.id('formulaire');

  formulaire.getElements('[type=text], [type=password], textarea').each(function(el){
    new OverText(el, {wrap: true});
  });

  new Form.Validator.Inline(formulaire, {
    onFormValidate: function(valid, formEl, submitEvent) {
      if (valid) {
        var query = Object.merge(formEl.toQueryString().trim().parseQueryString(),{'action': 'mod|user|auth'});
        submitEvent.stop();
        new Request.JSON({
          url: 'remoting.php',
          method: 'post',
          urlEncoded: true,
          onComplete: function(response) {
            if (response.message == 'BAD_LOGIN') {
              formEl.reset();
              $('resultat').set('html', 'Identifiants incorrects.');
            } else {
              document.location.href='index.php';
	          }
          },
	        data: { 
            json: JSON.encode(query)
	        }
	      }).send();
      }
    } 
  });

});
{/literal}

</script>
<form id="formulaire" method="post">
  <div id="resultat"></div>
  <fieldset>
    <legend>Identification</legend>
    <div>
      <div><input type="text" name="login" class="required" title="Utilisateur" /></div>
      <div><input type="password" name="passwd" class="required" title="Mot de passe" /></div>
    </div>
    <div><input type="submit" value="identification" id="btidentification"/></div>
  </fieldset>
</form>
