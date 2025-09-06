/*
Template Name: Minia - Admin & Dashboard Template
Author: Themesbrand
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Email Editor Js File
*/

// Get a reference to the CKEditor instance
const editor = ClassicEditor
    .create( document.querySelector( '#email-editor' ) )
    .then( function(editor) {
        editor.ui.view.editable.element.style.height = '200px';
    } )
    .catch( function(error) {
        console.error( error );
    });

// Reset the CKEditor instance
function resetEditor() {
    if (editor) {
        editor.destroy()
            .then( function() {
                // Recreate the CKEditor instance
                editor = ClassicEditor
                    .create( document.querySelector( '#email-editor' ) )
                    .then( function(newEditor) {
                        newEditor.ui.view.editable.element.style.height = '200px';
                    } )
                    .catch( function(error) {
                        console.error( error );
                    });
            } );
    }
}

// Call the resetEditor function to reset the CKEditor
resetEditor();

