+function($, win)
{
    $('.nav-sidebar').metisMenu();

	//Loads the correct sidebar on window load,
	//collapses the sidebar on window resize.
	$(win).bind("load resize", function() 
	{
		console.log($(this).width())
		if ($(this).width() < 768)
			$('.sidebar-collapse').addClass('collapse');
		else
			$('.sidebar-collapse').removeClass('collapse');
	});
	
	if($.validator)
	{
		$.validator.setDefaults({
		    highlight: function(element) {
		        $(element).closest('.form-group').addClass('has-error');
		    },
		    unhighlight: function(element) {
		        $(element).closest('.form-group').removeClass('has-error');
		    },
		    errorElement: 'span',
		    errorClass: 'help-block',
		    errorPlacement: function(error, element) {
		        if(element.parent('.input-group').length) {
		            error.insertAfter(element.parent());
		        } else {
		            error.insertAfter(element);
		        }
		    }
		});
	}
	
}(jQuery, window);