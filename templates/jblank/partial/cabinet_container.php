<div id="system_new_template_complete_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{CABINET-TEMPLATES-NEW}</h4>
            </div>
            <div class="modal-body preloader_block preloader_complete">
                {CABINET-TEMPLATES-NEW-COMPLETE}
            </div>
        </div>
    </div>
</div>

<div id="system_edit_template_complete_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{CABINET-TEMPLATES-UPDATE}</h4>
            </div>
            <div class="modal-body preloader_block preloader_complete">
                {CABINET-TEMPLATES-TEMPLATE-UPDATED}
            </div>
        </div>
    </div>
</div>

<div id="system_delete_template_complete_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{CABINET-TEMPLATES-EDIT-TEMPLATE-DELETING}</h4>
            </div>
            <div class="modal-body preloader_block preloader_complete">
                {CABINET-TEMPLATES-TEMPLATE-DELETED}
            </div>
        </div>
    </div>
</div>

<script>
    jQuery('#system_edit_template_complete_modal').on('hidden.bs.modal', function () {
        document.location.reload(true);
    });
    jQuery('#system_delete_template_complete_modal').on('hidden.bs.modal', function () {
        document.location.reload(true);
    });
</script>

<div id="system_new_template_modal" class="modal fade remittance" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" data-title_form="{CABINET-TEMPLATES-NEW}" data-title_template="{CABINET-TEMPLATES-UPDATE}"></h4>
            </div>
            <div class="modal-body preloader_block preloader_complete">
                <div class="preloader_content">


                </div>
                <img class="preloader" src="<?=$tpl->path?>/images/preloader.gif" />
            </div>
        </div>
    </div>
</div>
