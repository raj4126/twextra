/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/
//.............................default configuration..............................................................
//CKEDITOR.editorConfig = function( config )
//{
//	// Define changes to default configuration here. For example:
//	// config.language = 'fr';
//	// config.uiColor = '#AADC6E';
//};
//.................................recommended config...............................................................
CKEDITOR.editorConfig = function( config )
{
    config.toolbar = 'MyToolbar';

    config.toolbar_MyToolbar =
    [
      
        ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
        ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
        ['Link','Unlink'],
        ['TextColor','BGColor'],
        ['Maximize','-'],
        ['Image','Table','HorizontalRule','Smiley','SpecialChar'],
         '/',
        ['Styles','Format','Font','FontSize'],
        
        ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
        ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
      
    ];
};
//..................................................................................................
CKEDITOR.replace('editor1',
{
	//filebrowserBrowseUrl : '/uploader/browse.php',
	//filebrowserUploadUrl : '/scripts/ckeditor/upload.php'
});

//................................full config............................................................
//config.toolbar = 'Full';
//
//config.toolbar_Full =
//[
//    ['Source','-','Save','NewPage','Preview','-','Templates'],
//    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
//    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
//    ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
//    '/',
//    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
//    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
//    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
//    ['Link','Unlink','Anchor'],
//    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
//    '/',
//    ['Styles','Format','Font','FontSize'],
//    ['TextColor','BGColor'],
//    ['Maximize', 'ShowBlocks','-','About']
//];
//..............................basic config...............................................................
//config.toolbar_Basic =
//[
//    ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
//];
//.........................................................................................................

