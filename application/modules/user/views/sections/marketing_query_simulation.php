<div class="flexigrid" style="width: 100%;" data-unique-hash="ecd0d0e5a802371936b151b3bc1b7449">
    <div id="main-table-box" class="main-table-box">
        <div class="tDiv">
            <div class="tDiv3">
                <?php if (isset($current_table) && isset($ids)): ?>
                    <a class="export-anchor" data-url="" target="_blank">
                        <div class="fbutton">
                            <div>
                                <a href="<?php echo site_url('user/ajax/export_all?table=' . $current_table . '&ids=' . $ids) ?>"><span class="export">Exporter</span></a>
                            </div>
                        </div>
                    </a>
                <?php endif; ?>
                <div class="btnseparator"></div>
            </div>
            <div class="clear"></div>
        </div>
        
        <div id="ajax_list" class="ajax_list">
            <div class="bDiv">
                <?php $total_col = 6; ?>
                <table cellspacing="0" cellpadding="0" border="0" id="flex1">
                    <thead>
                        <?php foreach ($result AS $entity): ?>
                            <tr class="hDiv">
                                <?php
                                $item = get_object_vars($entity);
                                $keys = array_keys($item);
                                ?>
                                <?php foreach ($keys AS $key): ?>
                                    <th>
                                        <div class="text-left"><?php echo $key ?></div>
                                    </th>
                                <?php endforeach; ?>
                                <?php break; ?>    
                            </tr>
                        <?php endforeach; ?>
                    </thead>
                    <tbody>
                        <?php foreach ($result AS $entity): ?>
                            <tr>
                                <?php
                                $item = get_object_vars($entity);
                                $keys = array_keys($item);
                                ?>
                                <?php foreach ($keys AS $key): ?>
                                    <td>
                                        <div class="text-left"><?php echo $entity->$key ?></div>
                                    </td>
                                <?php endforeach; ?>                                
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>