+function($, win)
{
    $('.nav-sidebar').metisMenu();

	// //Loads the correct sidebar on window load,
	// //collapses the sidebar on window resize.
	$(win).bind("load resize", function() 
	{
		console.log($(this).width())
		if ($(this).width() < 768)
			$('.sidebar-collapse').addClass('collapse');
		else
			$('.sidebar-collapse').removeClass('collapse');
	});
	
}(jQuery, window);