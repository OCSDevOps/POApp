/**



 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.



 * For licensing, see LICENSE.md or http://ckeditor.com/license



 */







CKEDITOR.editorConfig = function( config ) {



	// Define changes to default configuration here. For example:



	// config.language = 'fr';



	// config.uiColor = '#AADC6E';

	config.filebrowserBrowseUrl = "../Cmspage/view_upload_list/";
	//config.filebrowserBrowseUrl = "/admincontrol/Cmspage/view_upload_list/";



	config.allowedContent = true;



	config.extraAllowedContent = 'div(*)';



	config.extraAllowedContent = 'span(*)';



	



	config.extraAllowedContent = 'div(col-md-*,container-fluid,row)';







//config.extraPlugins = 'filebrowser';











	config.toolbar = [



		{ name: 'document', items: [ 'Source'] },	



		{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },	



		{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },	



		{ name: 'forms', items: ['ImageButton','imageExplorer', 'HiddenField' ] },



		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },	



		



		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },	



		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },	



			



			



		



		{ name: 'insert', items: [ 'Image',  'Table', 'HorizontalRule'] },	



			{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },



		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },



		{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }



		



		



		



		



		 ];







};

CKEDITOR.replace('editor1');
CKEDITOR.replace('editor2');







