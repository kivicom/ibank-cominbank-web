<footer class="footer">
    <div class="container">
        <div class="left-side">
            <div class="left-side_logo">
                <a href="/">
                    <img src="<?=$tpl->pathTemplate?>/img/content/dvwhite.png">
                </a>
            </div>
            <div class="info-center">
                {HEADER-INFO-CENTER}<br>
                <a href="tel:{FOOTER-TELEPHONE}">{FOOTER-TELEPHONE}</a>
            </div>
        </div>
        <div class="right-side">
            <jdoc:include type="modules" name="position-footer" />
        </div>
        <div class="bottom-side">
            <div class="copy">
                <?php echo sprintf($I18N['{FOOTER-COPYRIHTS}'], JFactory::getDate()->format('Y') ); ?>
            </div>
            <div class="payforce">
                <?php echo sprintf($I18N['{FOOTER-DEVELOPED}'], $IB -> CONFIG -> get('DEVELOPER_URL') ); ?>
            </div>
        </div>
    </div>
</footer>

