<script type="text/javascript" src="<?php echo base_url('assets/libraries/grocery_crud/texteditor/ckeditor/ckeditor.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/libraries/grocery_crud/texteditor/ckeditor/adapters/jquery.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/libraries/grocery_crud/js/jquery_plugins/config/jquery.ckeditor.config.js') ?>"></script>
<!--<script type="text/javascript" src="<?php echo base_url('assets/backend/js/jquery-ui-timepicker-addon.js') ?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/backend/css/jquery-ui-timepicker-addon.css') ?>" />-->
<body onload="parent.alertsize(document.body.scrollHeight);" style="margin:0;">
	<?php echo isset($output) ? $output : ''; ?>
</body>
<script type="text/javascript">
	$(document).ready(function(){
		



		$('.change_select').on("change",function(){
		
			if($(this).val() == '0' ||  $(this).val() ==0){
				$("#autre_id").show();
			
			}
			else{
			
				$("#autre_id").hide();
			}
		
		});


	});

</script>