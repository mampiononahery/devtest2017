<script type="text/javascript" src="<?php echo base_url('assets/libraries/grocery_crud/texteditor/ckeditor/ckeditor.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/libraries/grocery_crud/texteditor/ckeditor/adapters/jquery.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/libraries/grocery_crud/js/jquery_plugins/config/jquery.ckeditor.config.js') ?>"></script>
<!--<script type="text/javascript" src="<?php echo base_url('assets/backend/js/jquery-ui-timepicker-addon.js') ?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/backend/css/jquery-ui-timepicker-addon.css') ?>" />-->
<script type="text/javascript">
    $(function () {
//        $('.picker').datetimepicker({
//            dateFormat: 'yy-mm-dd',
//            hourMin: 8,
//            hourMax: 20,
//            hourText: 'Heure',
//            minuteText: 'Minute',
//            closeText: 'Terminer',
//            showSecond: false,
//            showTimezone: false,
//            showMillisec: false,
//            showTime: false
//        });
    })
</script>
<?php echo isset($output) ? $output : ''; ?>