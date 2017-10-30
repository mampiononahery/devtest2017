<script type="text/javascript">
    $(function () {
        <?php if (isset($state) && $state != 'list' && $state != 'read' && $state != 'success') : ?>
        $('.datetime-input').datetimepicker('destroy'); // RESET DATETIMEPICKER
        $('.datetime-input').datetimepicker({
            timeFormat: 'HH:mm',
            dateFormat: js_date_format,
            showButtonPanel: true,
            showSecond: false,
            changeMonth: true,
            changeYear: true,
            minDate: 0
        });
        <?php endif; ?>
        $("#text-layout1").on('click', function () {
            var content = $('textarea#field-message').val() + "" + $('#text-layout1').text();
            $("#field-message").val(content);
        });
        $("#text-layout2").on('click', function () {
            var content = $('textarea#field-message').val() + "" + $('#text-layout2').text();
            $("#field-message").val(content);
        });
    });
</script>
<div id="ajax-loader">
    <img width="60" src="<?php echo site_url('assets/frontend/design/icons/loader.gif') ?>" alt="loader" />
</div>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo site_url('user/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo site_url('user/marketing/request') ?>">Marketing</a></li>
    <li class="breadcrumb-item active">Campagne SMS</li>
</ol>
<?php if (isset($state) && $state != 'list' && $state != 'read' && $state != 'read') : ?>
    <p class="bg-yellow">Pour le texte de votre campagne, vous pouvez utiliser les textes suivants: <br />
        <strong id="text-layout1">%NOM%</strong> (pour d&eacute;signer le <strong>nom du client</strong>);
        <strong id="text-layout2">%PRENOM%</strong> (pour d&eacute;signer le <strong>pr&eacute;nom du client</strong>);<br />
        <strong>Cliquez</strong> sur le texte pour l'ajouter &agrave; l'&eacute;diteur.
    </p>
    <p class="bg-yellow">Message limit&eacute; &agrave; 140 caract&egrave;res</p>
    <div class="alerte alert-warning">
        <p>Attention, il n'est possible d'annuler une campagne sauf si la date d'envoi est diff&eacute;r&eacute;e.
            Pour annuler, vous devez notifier l'administrateur, car l'annulation n'est pas automatique.</p>                        
    </div>
<?php endif; ?>
<?php echo isset($output) ? $output : ''; ?>