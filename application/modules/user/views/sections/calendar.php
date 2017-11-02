<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo site_url('user/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Mon agenda</li>
</ol>
<div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:100%;'>
    <div class="dhx_cal_navline">
        <div class="dhx_cal_prev_button">&nbsp;</div>
        <div class="dhx_cal_next_button">&nbsp;</div>
        <div class="dhx_cal_today_button"></div>
		
        <div class="calendar_filter">
		
		
		<select id="select_resource">
			<option value="0">Tous</option>
			<?php if($resources) { 
			
			foreach($resources as $res) {
				?>
					<option value="<?php echo $res->resource_id; ?>" ><?php echo $res->name; ?></option>
				<?php 
				}
			
			} ?>
		
		  </select>
        </div>
        <div class="dhx_cal_date"></div>
        <div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>
        <div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
        <div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>
    </div>
    <div class="dhx_cal_header"></div>
    <div class="dhx_cal_data"></div>
</div>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
		var resource_val = 0; 
        init(resource_val);
    });
	
	
	$("#select_resource").on("change",function(){
			
			scheduler.clearAll();
			init($(this).val());
			return true;
	});
	
	
    function init(id_ressource) {
        var step = 15;
        var format = scheduler.date.date_to_str("%H:%i");

        scheduler.config.hour_size_px = (60 / step) * 22;
        scheduler.templates.hour_scale = function (date) {
            html = "";
            for (var i = 0; i < 60 / step; i++) {
                html += "<div style='height:22px;line-height:22px;'>" + format(date) + "</div>";
                date = scheduler.date.add(date, step, "minute");
            }
            return html;
        }

        scheduler.config.multi_day = false;
        scheduler.config.xml_date = "%Y-%m-%d %H:%i";
        scheduler.config.first_hour = 8;
        scheduler.config.last_hour = 19;
        scheduler.config.time_step = 15;
        scheduler.config.hour_size_px = 88
        scheduler.config.separate_short_events = true;

        scheduler.config.limit_time_select = true;
        scheduler.config.details_on_create = true;
        scheduler.config.separate_short_events = true;
        scheduler.config.details_on_dblclick = true;
        scheduler.config.icons_select = ["icon_details", "icon_delete"];

        scheduler.locale.labels.section_location = "Location";
        scheduler.locale.labels.section_ref = "Assigné à";
        scheduler.locale.labels.section_sms = "Notifier le client par SMS";
        scheduler.locale.labels.section_clt = "Non présentation du client";

        scheduler.xy.min_event_height = 21; // 30 minutes is the shortest duration to be displayed as is

        scheduler.config.lightbox.sections = [
        {name: "client", height: 23, type: "client_view", focus: true},
        {name: "description", height: 130, map_to: "text", type: "textarea", focus: true},
        {name: "production", height: 43, type: "production", map_to: "details"},
<?php if (isset($resources)): ?>
            {name: "ref", height: 50, options: [
    <?php foreach ($resources AS $item): ?>
        <?php echo "{key: '" . $item->resource_id . "', label: '" . $item->name . "'" . "}," ?>
    <?php endforeach; ?>], map_to: "id_ressource", type: "radio"},
<?php endif; ?>
        {name: "sms", map_to: "sms", options: [{key: '1'}], type: "checkbox", checked_value: 1, unchecked_value: 0, height: 40},
        {name: "clt", map_to: "clt", options: [{key: '1'}], type: "checkbox", checked_value: 1, unchecked_value: 0, height: 40},
        {name: "time", height: 72, type: "time", map_to: "auto"}];
                /* CUSTOM FIELDS */
                scheduler.form_blocks["client_view"] = {
            render: function () {
                var html = '<div class="dhx_cal_ltext" style="height:auto;">';
                html += '<input type="text" placeholder="Client" size="25" class="client" id="client">';
                html += '<input type="hidden" size="25" class="client_id" id="client_id">';
                html += '</div>';
                return html;
            },
            set_value: function (node, value, ev) {
                node.childNodes[0].value = value || "";
                $.each(node.childNodes, function (index, item) {
                    if ($(item).attr("id") == "client") {
                        node.childNodes[index].value = ev.client_nom || "";
                    }
                    if ($(item).attr("id") == "client_id") {
                        node.childNodes[index].value = ev.id_client || "";
                    }
                });
            },
            get_value: function (node, ev) {
                $.each(node.childNodes, function (index, item) {
                    if ($(item).attr("id") == "client") {
                        ev.nom_client = node.childNodes[index].value;
                    }
                    if ($(item).attr("id") == "client_id") {
                        ev.id_client = node.childNodes[index].value;
                    }
                });
                return node.childNodes[0].value;
            },
            focus: function (node) {
            }
        } //end autocomplete client

        scheduler.form_blocks["production"] = {// production block
            render: function (sns) {
                var html = '<div class="dhx_cal_ltext">';
                html += '<input type="text" class="produits" id="produits"/>';
                html += "</div>";
                html += '<div id = "selectProduct"></div>';
                return html;
            },
            set_value: function (node, value, ev) {

            },
            get_value: function (node, ev) {

            },
            focus: function (node) {

            }
        };
        scheduler.attachEvent("onLightbox", function (event_id) {
            var _table = "<table>";
            _table += "<thead><tr>";
            _table += "<th>Libelle</th>";
            _table += "<th>PU TTC</th>";
            _table += "<th>Quantité</th>";
            _table += "<th>Remise</th>";
            _table += "<th>Prix total TTC</th>";
            _table += "</tr></thead><tbody>";
            _table += "<tr  class = 'product-item'>";
            _table += "<td colspan = '4'></td>";
            _table += "<td class = 'allTot'></td>";
            _table += "</tr>";
            _table += "</tbody></table>";
            $("#selectProduct").html(_table);

            $("input[type=checkbox]").switchButton({
                width: 118,
                height: 24,
                labels_placement: "left",
                button_width: 60,
                on_label: 'oui',
                off_label: 'non',
            });

            var switch_sms = 1;
            var switch_clt = 0;

            $.ajax({
                url: "<?php echo site_url('user/ajax/generateJsonResponseEdit') ?>",
                type: "post",
                dataType: "json",
                data: {
                    event_id: event_id,
                },
                success: function (data) {
                    if (data.id > 0) {
                        if (data.clt === "0") {
                            switch_clt = 0;
                        }
                        if (data.sms === "0") {
                            switch_sms = 0;
                        }
                    }
                    console.log("Begin - onLightbox");
                    console.log(data);
                    console.log("End - onLightbox");

                    var _table = "<table><thead>";
                    _table += "<tr>";
                    _table += "<th>Libelle</th>";
                    _table += "<th>PU TTC</th>";
                    _table += "<th>Quantité</th>";
                    _table += "<th>Remise %</th>";
                    _table += "<th>Prix total TTC</th>";
                    _table += "</tr></thead><tbody>";

                    $.each(data, function (key, value) {
                        if (value.produit) {
                            $('#client').val(value.nom_client);
                            $('#client_id').val(value.id_client);

                            var produits = value.produit;
                            var total = 0;
                            var selected = 0;
                            $.each(produits, function (k, v) {
                                var pu = v.pu;
                                var libelle = v.libelle;
                                var qte = v.qte;
                                var remise = parseFloat(v.remise);
                                var p_ttc = parseFloat(v.qte) * parseFloat(v.pu) * (1 - remise / 100);
                                p_ttc = Math.round(p_ttc * 100) / 100;
                                total = total + p_ttc;

                                var idProduct = k + 1;

                                _table += "<tr  class = 'product-item product_" + v.id_prd + "'>";
                                _table += "<td class = 'libelle'><input type='text' id='prodlibelle_" + idProduct + "' value='" + libelle + "' class='prod_libelle'readonly=''></td>";
                                _table += "<td class = 'prixttcwrapper'><input type='text' name = 'prod_prix_ttc' id='prod_prix_ttc_" + idProduct + "' value='" + pu + "' class='prod_prix_ttc' readonly=''</td>";
                                _table += "<td class = 'qtewrapper'>";
                                _table += "<select name='qte' class='qte' id='qte_" + idProduct + "'>";
                                for (i = 1; i < 21; i++) {
                                    if (qte == i)
                                        selected = "selected";
                                    else
                                        selected = "";
                                    _table += "<option value='" + i + "' " + selected + ">" + i + "</option>";
                                }
                                _table += "</select>";
                                _table += "</td>";
                                _table += "<td class = 'remisewrapper'>";
                                _table += "<div><input type = 'text' name='remise' class='remise' id='remise_" + idProduct + "' value = '" + remise + "'>";
                                _table += "<span class = 'r-plus'>+</span>";
                                _table += "<span class = 'r-moins'>-</span>";
                                _table += "</div></td>";
                                _table += "<td class = 'totalttcwrapper'><input type='text' id='prixttc_" + idProduct + "' value = '" + p_ttc + "' class='prix_ttc'readonly=''>";
                                _table += "<input type='hidden' id='id_produit" + idProduct + "' class='id_produit'readonly='' value='" + v.id_prd + "' style='width:40px'></td>";
                                _table += "</tr>";
                            });
                            _table += "<tr  class = 'product-item'>";
                            _table += "<td colspan = '4'></td>";
                            _table += "<td class = 'allTot'>" + total + "</td>";
                            _table += "</tr></tbody>";
                            _table += "</table>";
                            $("#selectProduct").html(_table);
                        }

                        $('.dhx_cal_radio label').removeClass('active');

                        var radio_selected = false;
                        $("input[name='ref']").each(function (index) {
                            if ($(this).is(':checked')) {
                                $(".dhx_cal_radio label").eq(index).addClass('active');
                                radio_selected = true;
                            }
                        });

                        if (!radio_selected) {
<?php if (isset($resources)): ?>
    <?php foreach ($resources AS $item): ?>
        <?php if ($item->is_default): ?>
                                        $('input[name="ref"]').each(function () {
                                            if ($(this).val() == <?php echo $item->resource_id ?>) {
                                                $(this).addClass("active");
                                                var radio_id = $(this).attr("id");
                                                $("label[for='" + radio_id + "']").addClass("active");
                                                return false;
                                            }
                                        });
            <?php break; ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
                        }
                    });
                },
                error: function () {
                    alert("Erreur pendant le chargement...");
                }
            });

            if (switch_sms) {
                $("input[name='sms']").switchButton({
                    checked: true
                });
            }
            if (switch_clt) {
                $("input[name='clt']").switchButton({
                    checked: true
                });
            }

            $(".client").autocomplete({
                minLength: 2, // only start searching 5 digits into the phone number
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo site_url('user/ajax/search_client') ?>",
                        type: "POST",
                        dataType: "json",
                        data: {
                            mask: $("#client").val(),
                            '677ubdbs7s7busbfisf8sbfs_some_post_token_to_prevent_csrf': "1"
                        },
                        success: function (data) {
                            response(jQuery.map(data, function (item) {
                                return {
                                    nom: item.nom,
                                    prenom: item.prenom,
                                    adresse: item.adresse,
                                    pays: item.pays,
                                    client_id: item.client_id
                                }
                            }));
                        }
                    });
                },
                focus: function (event, ui) {
                    var text = ui.item.nom + " " + ui.item.prenom;
                    $(".client").val(text);
                    return false;
                },
                select: function (event, ui) {
                    $("#nom").val(ui.item.nom);
                    $("#prenom").val(ui.item.prenom);
                    $("#adresse").val(ui.item.adresse);
                    $("#pays").val(ui.item.pays);
                    $("#client_id").val(ui.item.client_id);
                    return false;
                }
            }).data("ui-autocomplete")._renderItem = function (ul, item) {
                return $("<li></li>").data("item.autocomplete", item)
                        .append("<a>" + item.nom + " " + item.prenom + "</a>")
                        .appendTo(ul);
            };

            $("#produits").autocomplete({
                minLength: 1,
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo site_url('user/ajax/search_production') ?>",
                        type: "POST",
                        dataType: "json",
                        data: {
                            mask: $("#produits").val(),
                            '677ubdbs7s7busbfisf8sbfs_some_post_token_to_prevent_csrf': "1"
                        },
                        success: function (data) {
                            response($.map(data, function (item) {
                                return {
                                    prod_libelle: item.prod_libelle,
                                    prod_prix_ttc: item.prod_prix_ttc,
                                    prod_id: item.prod_id
                                }
                            }));
                        }
                    });
                },
                focus: function (event, ui) {
                    $("#produits").val($(ui.item.prod_libelle).text());
                    return false;
                },
                select: function (event, ui) {
                    libProduct = ui.item.prod_libelle;
                    prixProduct = ui.item.prod_prix_ttc;
                    idProduct = parseInt($('.product-item').length);
                    $('#produits').val("");
                    var _length = $("#selectProduct").find('.product_' + ui.item.prod_id).length;
                    if (!_length) {
                        var _html = "<tr  class = 'product-item product_" + ui.item.prod_id + "'>";
                        _html += "<td class='libelle'><input type='text' id='prodlibelle_" + idProduct + "' value='" + libProduct + "' class='prod_libelle' readonly=''></td>";
                        _html += "<td class='prixttcwrapper'><input type='text' name = 'prod_prix_ttc' id='prod_prix_ttc_" + idProduct + "' value='" + prixProduct + "' class='prod_prix_ttc' readonly=''</td>";
                        _html += "<td class='qtewrapper'>";
                        _html += "<select name='qte' class='qte' id='qte_" + idProduct + "'>";
                        for (i = 1; i < 21; i++) {
                            _html += "<option value='" + i + "'>" + i + "</option>";
                        }
                        _html += "</select>";
                        _html += "</td>";
                        _html += "<td class = 'remisewrapper'>";
                        _html += "<div><input type = 'text'  name='remise' class='remise' id='remise_" + idProduct + "' value = '0'>";
                        _html += "<span class = 'r-plus'>+</span>";
                        _html += "<span class = 'r-moins'>-</span>";
                        _html += "</div></td>";
                        _html += "<td class = 'totalttcwrapper'><input type='text' id='prixttc_" + idProduct + "' value = '" + prixProduct + "' class='prix_ttc'readonly=''>";
                        _html += "<input type='hidden' id='id_produit" + idProduct + "' class='id_produit'readonly='' value='" + ui.item.prod_id + "' style='width:40px'></td>";
                        _html += "</tr>";
                        $("#selectProduct table tbody").prepend(_html);
                    } else {
                        var value = $("#selectProduct").find('.product_' + ui.item.prod_id).find(".qte").val();
                        var newval = parseInt(value) + 1;
                        $("#selectProduct").find('.product_' + ui.item.prod_id).find(".qte option[value='" + newval + "']").prop('selected', true);
                        $("#selectProduct").find('.product_' + ui.item.prod_id).find(".qte").trigger('change');
                    }
                    allTot = 0;
                    $(".totalttcwrapper .prix_ttc").each(function (index) {
                        allTot = parseFloat($('.totalttcwrapper .prix_ttc').eq(index).val()) + allTot;
                    });
                    $('.allTot').text(allTot);
                    return false;
                }
            }).data("ui-autocomplete")._renderItem = function (ul, item) {
                return $("<li></li>").data("item.autocomplete", item)
                        .append("<a>" + item.prod_libelle + " | Prix: " + item.prod_prix_ttc + " &euro;</a>")
                        .appendTo(ul);
            };

        });
        /* END CUSTOM FIELDS */

        var dragged_ev = null;
        scheduler.attachEvent("onBeforeDrag", function (ev, mode) {
            if (mode === 'move') {
				console.log("ggg");
                dragged_ev = scheduler.getEvent(ev);
				dragged_event = scheduler.getEvent(ev);
            }
			console.log("ggg");
            return true;
        });

        /* EVENT ACTIONS */
        scheduler.attachEvent("onEventSave", function (id, ev, is_new_event) {
			console.log(ev);
			console.log(is_new_event);



		   var inputLength = $(".prod_prix_ttc").length;

            $(".id_produit").each(function () {
                scheduler.setUserData(id, $(this).attr('id'), $(this).val());
            });
            $(".prod_prix_ttc").each(function () {
                scheduler.setUserData(id, $(this).attr('id'), $(this).val());
            });
            $(".prod_libelle").each(function () {
                scheduler.setUserData(id, $(this).attr('id'), $(this).val());
            });
            $(".qte").each(function () {
                scheduler.setUserData(id, $(this).attr('id'), $(this).val());
            });
            $(".remise").each(function () {
                scheduler.setUserData(id, $(this).attr('id'), $(this).val());
            });
            $(".prix_ttc").each(function () {
                scheduler.setUserData(id, $(this).attr('id'), $(this).val());
            });
            scheduler.setUserData(id, "inputLength", inputLength);

            var custom_ev = scheduler.getEvent(scheduler.getState().lightbox_id);

           

            custom_ev.text = ev.text;
            custom_ev.id_client = ev.id_client;
            custom_ev.id_ressource = ev.id_ressource;
            custom_ev.nom_client = ev.nom_client;
            custom_ev.sms = ev.sms;
            custom_ev.clt = ev.clt;

            /* PARSE DATE */
            var formatFunc = scheduler.date.date_to_str("%Y-%m-%d %h:%i:%s");
            custom_ev.start_date = formatFunc(new Date(ev.start_date));
            custom_ev.end_date = formatFunc(new Date(ev.end_date));
            /* END PARSE DATE */

            if (is_new_event) {
                custom_ev.is_new = 1; // new event
            } else {
                custom_ev.is_new = 0;
            }

            $.ajax({
                url: "<?php echo site_url('user/ajax/save_event') ?>",
                type: "POST",
                dataType: "json",
                data: {'data': JSON.stringify(custom_ev)},
                success: function (data) {
                    
                },
                error: function () {
                    alert("error during process...");
                }
            });

            return true;
        });
		scheduler.attachEvent("onDragEnd", function(event,mod){
			//var event_obj = dragged_event;
			/**
			* AJAX TO SEND VERS  apres ON DRAG EVENT
			*/
			
			/*console.log(mod);
			console.log(event);*/
			if(mod=="move"){
				$.ajax({
					url: "<?php echo site_url('user/ajax/save_event') ?>",
					type: "POST",
					dataType: "json",
					data: {'data': JSON.stringify(dragged_event)},
					success: function (data) {
						
					},
					error: function () {
						alert("error during process...");
					}
				});
			}
			//save_event(event_obj);
			return true;
		});
	
        scheduler.attachEvent("onEventDeleted", function (id) {
           

            $.ajax({
                url: "<?php echo site_url('user/ajax/delete_event') ?>",
                type: "POST",
                dataType: "json",
                data: {event_id: id},
                success: function (data) {
                    
                },
                error: function () {
                    alert("error during process...");
                }
            });
        });
        /* END EVENT ACTIONS */

        scheduler.templates.event_class = function (start, end, event) {
            var css = "";
            if (event.id_ressource) { // if event has subject property then special class should be assigned
                css += "my_event event_" + event.id_ressource;
            } else {
                css += "my_event event_1";
            }
            if (event.id == scheduler.getState().select_id) {
                css += " selected";
            }
            return css;
        };

        scheduler.renderEvent = function (container, ev, width, height, header_content, body_content) {
            var container_width = container.style.width; // e.g. "105px"

            // move section
            var html = "<div class='dhx_event_move my_event_move' style='width: " + container_width + "'></div>";
            // container for event contents
            html += "<div class='my_event_body'>";
            html += ev.nom_client;
            if (ev.sms === "1") { // rdv sms notification allowed
                $.ajax({
                    url: "<?php echo site_url('user/ajax/checkEventNotificationStatus') ?>",
                    type: "POST",
                    dataType: "json",
                    data: {event_id: ev.id},
                    success: function (data) {
                        if (data.message_sent) {
                            html += '<img class="sms_sent" src="<?php echo site_url('assets/backend/design/message_sent.png') ?>" title="SMS de notification de RDV envoyé" alt="SMS de notification de RDV envoyé"/>';
                        }
                        if (data.message_scheduled) {
                            html += '<img class="sms_scheduled" src="<?php echo site_url('assets/backend/design/message.png') ?>" title="SMS planifié" alt="SMS planifié"/>';
                        }
                        if (data.message_error) {
                            html += '<img class="sms_not_sent" src="<?php echo site_url('assets/backend/design/message_error.png') ?>" title="SMS non envoyé" alt="SMS non envoyé"/>';
                        }
						else{
						
							  html += '<img class="sms_not_sent" src="<?php echo site_url('assets/backend/design/message_error.png') ?>" title="SMS non envoyé" alt="SMS non envoyé"/>';
						}
                    },
                    error: function () {
                        alert("error during process...");
                    }
                });
            }

            if (ev.clt === "1") {
                html += '<img class="rdv_not_honored" src="<?php echo site_url('assets/backend/design/not_honored.png') ?>" title="Client absent au RDV" alt="Client absent au RDV"/>';
            }
			html += '<img class="rdv_not_honored" src="<?php echo site_url('assets/backend/design/message.png') ?>" title="Client absent au RDV" alt="Client absent au RDV"/>';
			
			
            html += "</div>";

            // resize section
            html += "<div class='dhx_event_resize my_event_resize' style='width: " + container_width + "'></div>";

            container.innerHTML = html;
            return true; // required, true - we've created custom form; false - display default one instead
        };

        scheduler.templates.tooltip_text = function (start, end, event) {
             
            return "<b>Commentaire:</b> " + event.text + "<br/><b>Production:</b>"+event.production+"<br/><b>Heure de début:</b> " +
                    format(start) + "<br/><b>Heure de fin:</b> " + format(end);
        
        };

        <?php $date_focus = date('Y') . ',' . (date('m') > 0 ? date('m') - 1 : date('m')) . ',' . date('d'); ?>
        scheduler.init('scheduler_here', new Date(<?php echo $date_focus ?>), "week");
        //scheduler.load("<?php echo site_url('user/ajax/generateJsonResponse') ?>", "json");
		
		
		 scheduler.load("<?php echo site_url('user/ajax/generateJsonResponse') ?>?id_ressource="+id_ressource, "json");
		
        window.alert = function () {
            scheduler.clearAll();
            scheduler.load("<?php echo site_url('user/ajax/generateJsonResponse') ?>", "json");
        };
    }
</script>