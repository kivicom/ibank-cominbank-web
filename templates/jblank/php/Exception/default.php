<?php
//header('Location: /cabinet/oops');

//print_r($this -> EXCEPTION); die;

?>
    <div class="global-errors">
        ERROR <?php if (isset($this -> EXCEPTION -> errorDescription)) {echo "(".$this -> EXCEPTION -> errorDescription.")";}?>
    </div>

<?php

$this -> EXCEPTION = '';

?>