<h3>Set Modules default parameters</h3>
<ul id="config-menu" class="topbar-inner nav span18">  
	<li><a href="#" class="top-btn active">Rules</a></li>
	<li><a href="#" class="top-btn">Registration</a></li>
	<li><a href="#" class="top-btn">Email</a></li>
	<li><a href="#" class="top-btn">Other</a></li>
</ul>

<div id="config-rules" class="clearfix span18">
	<div id="rules" class="params row clearfix active" style="padding-left: 20px; min-height: 25em">
		<div class="left-pane span3">
			<h3>choose ...</h3>
			<div class="select">
				<div class="row select link checked"><div><h3>Basic</h3></div></div>
				<div class="row select link"><div><h3>Web</h3></div></div>
				<div class="row select link"><div><h3>Application</h3></div></div>
				<div class="row select"><div><h3>Export</h3></div></div>
				<div class="row select"><div><h3>Import</h3></div></div>
			</div>
		</div>	
  		<div class="well right-pane clearfix span14" style="margin-top: -1px; min-height: 18.8em">
			<div class="alert alert-error">
				<a class="link close" onclick="this.getParent('.alert.alert-error').slide('out').fade('out');">&times;</a>
  				<h4 class="alert-heading">Caution</h4>
				this setup change the way users, user groups and permissions are managed
			</div>
	
			<div id="basic" class="secondary active">
				<h3>Basic rules</h3>
					<div class="row">
						<div class="left alert alert-block span3">
							<i class="icon-edit right link">&times;</i>
  							<h4 class="alert-heading">Groups</h4>
  							<strong>Registered</strong> / <strong>Admin</strong> 
						</div>
						<div class="right alert alert-success span6">
							<i class="icon-edit right link">&times;</i>
  							<h4 class="alert-heading">Registered</h4>
  							<strong>Registered</strong> can view object
  							<h4 class="alert-heading">Admin</h4>
							<strong>Admin</strong> can add/edit/delete object
						</div>
					</div>
					<div class="alert alert-info">
						<i class="icon-edit right link">&times;</i>
  						<h4 class="alert-heading">Permissions</h4>
  						<strong>Registered</strong> can view object
						<strong>Admin</strong> can add/edit/delete object
					</div>
					<a class="left link btn primary">Save</a>
					<a class="right link btn">Cancel</a>
			</div>
			<div id="web" class="secondary">
				<h3>Web rules</h3>
				<div class="row">
					<div class="left alert alert-block span3">
						<i class="icon-edit right link">&times;</i>
  						<h4 class="alert-heading">Groups</h4>
  						<strong>Anonymous</strong> / 
						<strong>Registered</strong> / 
						<strong>Admin</strong>
					</div>
					<div class="right alert alert-success span6">
						<i class="icon-edit right link"></i>
	  					<h4 class="alert-heading">Anonymous</h4>
		  				<strong>Anonymous</strong> can view object
  						<h4 class="alert-heading">Registered</h4>
  						<strong>Registered</strong> can add/edit/delete object
  						<h4 class="alert-heading">Admin</h4>
						<strong>Admin</strong> can admin object
					</div>
				</div>
				<div class="alert alert-info">
					<i class="icon-edit right link"></i>
  					<h4 class="alert-heading">Permissions</h4>
  					<strong>Anonymous</strong> can view object
  					<strong>Registered</strong> can add/edit/delete object
					<strong>Admin</strong> can admin object
				</div>
				<a class="left link btn primary">Save</a>
				<a class="right link btn">Cancel</a>

			</div>
			<div id="application" class="secondary">
				<h3>Application rules</h3>
				<div class="row">
					<div class="left alert alert-block span3">
  						<a class="close">×</a>
		  				<h4 class="alert-heading">Groups</h4>
  						<strong>Anonymous</strong> / 
						<strong>Registered</strong> / 
						<strong>Admin</strong>
					</div>
					<div class="right alert alert-success span6">
						<a class="close">×</a>
	  					<h4 class="alert-heading">Anonymous</h4>
		  				<strong>Anonymous</strong> can view object
  						<h4 class="alert-heading">Registered</h4>
  						<strong>Registered</strong> can add/edit/delete object
		  				<h4 class="alert-heading">Admin</h4>
						<strong>Admin</strong> can admin object
					</div>
				</div>
				<div class="alert alert-info">
					<a class="close">×</a>
		  			<h4 class="alert-heading">Permissions</h4>
  					<strong>Anonymous</strong> can view object
		  			<strong>Registered</strong> can add/edit/delete object
					<strong>Admin</strong> can admin object
				</div>
				<a class="left link btn primary">Save</a>
				<a class="right link btn">Cancel</a>
			</div>
			<div id="export" class="secondary">
				<h3>export rules set</h3>
				<a class="left link btn primary">Export</a>
				<a class="right link btn">Cancel</a>

			</div>
			<div id="import" class="secondary">
				<h3>import rules set</h3>
				<a class="left link btn primary">Import</a>
				<a class="right link btn">Cancel</a>

			</div>

  		</div>
	</div>	
  	<div id="registration" class="params row clearfix">
		<div class="left-pane span14">
		<h3>Registration</h3>
		</div>
	</div>
	<div id="email" class="params row clearfix">
		<div class="left-pane span14">
		<h3>Email</h3>
		</div>
	</div>
	<div id="other" class="params row clearfix">
		<div class="left-pane span14">
		<h3>Other</h3>
		</div>
	</div>


</div>
<script>
	window.addEvent('domready', function() {
		{literal}
		
		var mycvar = $('rules').getChildren('.right-pane .secondary').each(function(c) {
				c.slide('hide');
				if (c.hasClass('active'))	
					c.show().slide('toggle').fade('in');
		});
				
		var myvar = $('rules').getChildren('.left-pane .select .row.select');
		myvar.each(function(el) {
			el.addEvents({
    				click: function(e){
					var update = el.getChildren('div h3').get('html')[0].toLowerCase();
					el.getParent('.select').getChildren('.row.select').removeClass('checked');	
					el.toggleClass('checked').highlight();
					var myvar = $(update).getParents('div.right-pane .secondary').each(function(c) {
						if (c.hasClass('active')) 
							c.removeClass('active');
						if (!c.hasClass('hidden'))
							c.slide('out').fade('out');
				
					});
					$(update).show().slide('in').addClass('active').fade('in');
				}
			});
			
		});
		var mymenu = $('config-rules').getChildren('div.params').each(function(er) {
				er.slide('hide');
				if (er.hasClass('active'))	
					er.show().slide('in').fade('in');
				
		});
		var mybtnmenu = $('config-menu').getChildren('li').each(function(el) {
			el.addEvents({
    				click: function(e){
					var update = el.getChildren('a').get('html')[0].toLowerCase();
					el.getParent('ul').getChildren('li').removeClass('active');	
					el.toggleClass('active').highlight();
					console.log(update);
					var myvar = $(update).getParents('div.params').each(function(c) {
						if (c.hasClass('active')) {
							c.removeClass('active');
						}
						c.slide('hide').fade('out');
					});
					
					$(update).show().slide('toggle').addClass('active');
				}
			});
		});
		{/literal}
	});
</script>
{*
<div id="optionsTabs">
<ul class="tabs clearfix">  
	<li class="tab active"><a>Rules</a></li>
	<li class="tab"><a>Registration</a></li>
	<li class="tab"><a>Email</a></li>
	<li class="tab"><a>Other</a></li>
</ul>
<div id="rules" class="content active">
	<ul class="tabs">  
		<li class="tab active">Simple</li>
		<li class="tab">Web</li>
		<li class="tab">Custom</li>
	</ul>
	<div id="simple" class="content active">
		<h3>use simple user/admin rules</h3>
	</div>
	<div id="web" class="content">
		<h3>use anonymous/registered/admin rules</h3>
	</div>
	<div id="custom" class="content">
		<h3>use custom applicaton based rules</h3>
	</div>
</div>
<div id="registration" class="content">
	<ul class="tabs"> Optional User module parameters 
		<li class="tab dropdown active">Registration</li>
		<li>User can Register
			<ul class="dopdown-menu">
				<li>Registration group</li>
			</ul>
		</li>
		<li class="tab">CAPTCHA</li>
	</ul>
	<ul>
		<li>and confirm by email before activate the user </li>
		<li></li>
		<li>User can cancel and request a new password by email</li>
		<li>User can send invitations to register by email</li>
		<li>And add user to a group by default</li>
		<li></li>
		<li>After user logout, redirect user to:</li>
		<li>After user login, redirect user to:</li>
	</ul>
</div>
</div>
*}
