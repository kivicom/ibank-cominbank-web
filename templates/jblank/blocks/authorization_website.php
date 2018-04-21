<script>
    jQuery("body").on("click", ".authorization_website", function (e) {
        var action = jQuery(this).attr("href");
        var auth = <?=($IB -> TOKEN ? "true" : "false")?>;
        if (auth) {
            e.preventDefault();
            jQuery("#authorization_website input[name='redirect_url']").val(action);
            jQuery("#authorization_website").submit();
        }
    });
</script>

<form id="authorization_website" action="<?=($IB -> TOKEN ? $IB -> CONFIG -> get('URL_WEBSITE_AUTH') : $IB -> CONFIG -> get('URL_WEBSITE'))?>" method="POST">
    <input type="hidden" name="auth_token" value="<?=$IB -> TOKEN?>"/>
    <input type="hidden" name="redirect_url" value="<?=$IB -> CONFIG -> get('URL_WEBSITE')?>"/>
</form>