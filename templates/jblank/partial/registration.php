<?php
if (isset($_GET['err'])) {
    echo "ERROR: ".$_GET['err']."<br/><br/>";
}
?>

<form action="?SERVICE" method="post">
    <input type="hidden" name="args[0]" value="registerUser" />
    <input type="text" name="args[1]" placeholder="login" /><br/>
    <input type="text" name="args[2]" placeholder="Phone" /><br/>
    <input type="text" name="args[3]" placeholder="Password" /><br/>
    <input type="text" name="args[4]" placeholder="firstName" /><br/>
    <input type="text" name="args[5]" placeholder="lastName" /><br/>
    <input type="text" name="args[6]" placeholder="middleName" /><br/>
    <input type="text" name="args[7]" placeholder="email" /><br/>


    <button type="submit">Send</button>
</form>