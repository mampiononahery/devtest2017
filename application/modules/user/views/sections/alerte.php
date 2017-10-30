<?php if (isset($client)) : ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo site_url('user/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo site_url('user/client') ?>">Mes clients</a></li>
        <li class="breadcrumb-item active">Alertes</li>
    </ol>
    <div class="flexigrid content-wrapper">
        <div class="mDiv">
            <div class="ftitle">
                <div class="ftitle-left">Info client</div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="form-div">
            <ul>
                <li>Genre: <?php echo $client->genre ?></li>
                <li>Nom: <?php echo $client->nom ?></li>
                <li>Pr&eacute;nom: <?php echo $client->prenom ?></li>
            </ul>
        </div>
    </div>
<?php else: ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo site_url('user/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Alertes</li>
    </ol>
    <ul class="option_alert">
        <li>N'afficher que les alerts è traiter</li>
        <li>
            <div class="switch">
                <input class="switch-input" id="is_true" type="radio" value="1"/>
                <label for="is_true" class="switch-label switch-label-off">oui</label>
                <input class="switch-input" id="is_false" type="radio" value="0"/>
                <label for="is_false" class="switch-label switch-label-on">non</label>
                <span class="switch-selection"></span>
            </div>
        </li>
    </ul>
    
<?php endif; ?>
<?php echo isset($output) ? $output : ''; ?>
<?php if (isset($state) && $state == "read"): ?>
    <div class="flexigrid content-wrapper">
        <div class="form-div">
            <div class="form-field-box">
                <?php if (isset($current)): ?>
                    <script type="text/javascript">
                        $(function () {
                            $('.second-action-btn').on('click', function (e) {
                                e.preventDefault();
                                $source = $(this).attr('href');
                                $.ajax({
                                    url: $source,
                                    dataType: "json",
                                    success: function (data) {
                                        if (data.update_status) {
                                            window.location.replace('<?php echo site_url('user/alerte/index/read/' . $current->alerte_id); ?>');
                                        }
                                    },
                                    error: function () {
                                        alert("Erreur pendant le chargement...");
                                    }
                                });
                            });
                        });
                    </script>
                    <?php if (!$current->acquitted): ?>
                        <a class="second-action-btn" href="<?php echo site_url('user/ajax/close_alerte/' . $current->alerte_id); ?>"><button id="" class="btn btn-primary">Acquitter</button></a>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="<?php echo site_url('user/alerte/index/edit/' . $current->alerte_id); ?>"><button class="btn btn-success">Modifier</button></a>
                <?php if ($current->client_id > 0): ?>
                    <a href="<?php echo site_url('user/client/index/read/' . $current->client_id); ?>"><button class="btn btn-info">Fiche client</button></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
<?php endif; ?>
