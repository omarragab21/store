$(document).ready(function() {
	$(".numeric").keydown(function(e) {		
		 // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
	});
});
$(document).ready(function(){
    $( document ).on('focus', '.mask,.numeric', function(){
        $(this).attr( 'autocomplete', 'off' );
    });
});
$('.inputMaterial').focusout(function() {
    $('.group').removeClass('focus');    
    if(false && $(this).val().length < 1 && $(this).closest('.group').children().has('.xerror')){
    	$(this).closest('.group').addClass('has-error');
    	$(this).closest('.group').children('.xerror').removeClass('hidden');
    }    
});
$('.inputMaterial').focus(function() {
    $(this).closest('.group').addClass('focus');
    if(false){
    	$(this).closest('.group').removeClass('has-error');
    	$(this).closest('.group').children('.xerror').addClass('hidden');
    }
});
$('input.inputMaterial, textarea.inputMaterial').bind("change",function(){
	if($(this).val().length > 0){
        $(this).closest('.group').addClass('filled');
    }else{
        $(this).closest('.group').removeClass('filled');
    }
});
$('input.inputMaterial, textarea.inputMaterial').bind("change keyup",function(){
	if($(this).val().length > 0){
        $(this).closest('.group').addClass('filled');
    }else{
        $(this).closest('.group').removeClass('filled');
    }
});
$('select.inputMaterial').bind("change keyup",function(){	
   $(this).closest('.group').addClass('filled');    
});
/// Input Kepress Filled  Focus
$('input.inputMaterial, textarea.inputMaterial').keyup(function() {
    if($(this).val().length > 0){
        $(this).closest('.group').addClass('filled');
    }else{
        $(this).closest('.group').removeClass('filled');
    }
});

/// Input Check Filled Focus
var $formControl = $('input.inputMaterial, textarea.inputMaterial');
var values = {};
var validate =    $formControl.each(function() {
    if($(this).val() != null && $(this).val().length > 0){
        $(this).closest('.group').addClass('filled');
    }
    else{
        $(this).closest('.group').removeClass('filled');
    }
});