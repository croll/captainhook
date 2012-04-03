//  -*- mode:js; tab-width:2; c-basic-offset:2; -*-
CKEDITOR.stylesSet.add('captainhookStyles', [
  { name: 'Label', element: 'div', attributes: {'class': 'label'} },
  { name: 'Label success', element: 'div', attributes: {'class': 'label label-success'} },
  { name: 'Label info', element: 'div', attributes: {'class': 'label label-info'} },
  { name: 'Label warning', element: 'div', attributes: {'class': 'label label-warning'} },
  { name: 'Label error', element: 'div', attributes: {'class': 'label label-error'} },
  { name: 'Label inverse', element: 'div', attributes: {'class': 'label label-inverse'} },
  { name: 'Badge', element: 'div', attributes: {'class': 'badge'} },
  { name: 'Badge success', element: 'div', attributes: {'class': 'badge badge-success'} },
  { name: 'Badge inverse', element: 'div', attributes: {'class': 'badge badge-inverse'} },
  { name: 'Badge info', element: 'div', attributes: {'class': 'badge badge-info'} },
  { name: 'Badge warning', element: 'div', attributes: {'class': 'badge badge-warning'} },
  { name: 'Badge error', element: 'div', attributes: {'class': 'badge badge-error'} },
  { name: 'Button', element: 'span', attributes: {'class': 'btn'} },
  { name: 'Alert success', element: 'div', attributes: {'class': 'alert alert-success'} },
  { name: 'Alert info', element: 'div', attributes: {'class': 'alert alert-info'} },
  { name: 'Alert warning', element: 'div', attributes: {'class': 'alert alert-warning'} },
  { name: 'Alert error', element: 'div', attributes: {'class': 'alert alert-error'} },
  { name: 'Button primary', element: 'span', attributes: {'class': 'btn btn-primary'} },
  { name: 'Button info', element: 'span', attributes: {'class': 'btn btn-info'} },
  { name: 'Button success', element: 'span', attributes: {'class': 'btn btn-success'} },
  { name: 'Button warning', element: 'span', attributes: {'class': 'btn btn-warning'} },
  { name: 'Button danger', element: 'span', attributes: {'class': 'btn btn-danger'} },
  { name: 'Button inverse', element: 'span', attributes: {'class': 'btn btn-inverse'} }
]);

var CHWysiwyg = new Class({
		Implements: [Options,Events],
		options: {
			editorElement: 'editor1',
			active: true,
			ckConfig: 'chooseme',
			extraPlugins: 'selected',
			ckLang: 'fr',
			ckSkin: 'BootstrapCK-Skin',
			onComplete: null
		},
		initialize: function(options) {
			this.setOptions(options);
        		var editor1 = CKEDITOR.replace('editor1', {
				contentsCss : '/mod/cssjs/ext/twitter-bootstrap/css/bootstrap.css',
				stylesSet: 'captainhookStyles',
				language: 'fr',
				skin : 'BootstrapCK-Skin'
			});
		},
		insertHTML: function(options, ckInstance) {
			// Get the editor instance that we want to interact with.
			var oEditor = CKEDITOR.instances.editor1;
			var value = document.getElementById( 'htmlArea' ).value;

			// Check the active editing mode.
			if ( oEditor.mode == 'wysiwyg' ) {
				// Insert HTML code.
				// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html#insertHtml
				oEditor.insertHtml( value );
			} else { 
				CaptainHook.Message.show('Error You must be in WYSIWYG mode !' , 'wysywig', { width: 320 });
			}
		},
		insertText: function(options, ckInstance) {
			// Get the editor instance that we want to interact with.
			var oEditor = CKEDITOR.instances.editor1;
			var value = document.getElementById( 'txtArea' ).value;

			// Check the active editing mode.
			if ( oEditor.mode == 'wysiwyg' ) {
				// Insert as plain text.
				// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html#insertText
				oEditor.insertText( value );
			} else { 
				CaptainHook.Message.show('Wysiwyg' , 'You must be in WYSIWYG mode!', { width: 320 });
			}
		},
		prepareToSave: function(options) {
			var oEditor = CKEDITOR.instances.editor1;
			var content = oEditor.getData();
			content = CKEDITOR.tools.htmlEncode(content);
			return content;
		},
		setContents: function(options, ckInstance) {
			// Get the editor instance that we want to interact with.
			var oEditor = CKEDITOR.instances.editor1;
			var value = document.getElementById( 'htmlArea' ).value;

			// Set editor contents (replace current contents).
			// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html#setData
			oEditor.setData( value );
		},
		getContents: function(options, ckInstance) {
			// Get the editor instance that you want to interact with.
			var oEditor = CKEDITOR.instances.editor1;

			// Get editor contents
			// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html#getData
			var content = oEditor.getData();
			CaptainHook.Message.show(content , 'wysywig' , { width: 600 });
		},
		getContent: function(options, ckInstance) {
			// Get the editor instance that you want to interact with.
			var oEditor = CKEDITOR.instances.editor1;

			// Get editor contents
			// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html#getData
			var content = oEditor.getData();
			return content;
		},
		executeCommand: function(commandName) {
			// Get the editor instance that we want to interact with.
			var oEditor = CKEDITOR.instances.editor1;

			// Check the active editing mode.
			if ( oEditor.mode == 'wysiwyg' ) {
				// Execute the command.
				// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html#execCommand
				oEditor.execCommand( commandName );
			}
		},
		checkDirty: function(options, ckInstance) {
			// Get the editor instance that we want to interact with.
			var oEditor = CKEDITOR.instances.editor1;
			// Checks whether the current editor contents present changes when compared
			// to the contents loaded into the editor at startup
			// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html#checkDirty
			var res = oEditor.checkDirty();
			CaptainHook.Message.show(res , 'tutu'  , { width: 320 });
		},
		resetDirty: function(options, ckInstance) {
			// Get the editor instance that we want to interact with.
			var oEditor = CKEDITOR.instances.editor1;
			// Resets the "dirty state" of the editor (see CheckDirty())
			// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html#resetDirty
			oEditor.resetDirty();
			CaptainHook.Message.show('The "IsDirty" status has been reset' , 'The "IsDirty" status has been reset'  , { width: 320 });
		}
});

