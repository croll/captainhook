//  -*- mode:js; tab-width:2; c-basic-offset:2; -*-
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

