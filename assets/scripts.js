  	
	   function WC_FFM_deletelistnow(){

		if(!confirm('Do you really want to delete this?')){
		return false;
		}

		jQuery.ajax({
		type:"POST",
		url: "/wp-admin/admin-ajax.php",
		dataType: 'json',
		crossDomain: true,
		data: { action: "WC_FFM_deletelist" },
		//beforeSend: function() {
//				
//			},
		success:function(data){
		
		location.reload();
		
		}
		
		});
		
			   }

	 