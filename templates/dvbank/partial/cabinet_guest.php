<?php
if(!$IB->userRole('full')) { ?>

    <div class="guest-text">
        <?=$text?>
    </div>

<?php } ?>