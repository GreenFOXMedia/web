$(document).ready(function(){
	Selection = $("#select_page");
	startValue = Selection.val();
	console.log(startValue);
	$('#select_page').on('change', function() {
	  $("input[name='belongs_to_page']").val($("#select_page option:selected").val()); // or 
	});	
})
