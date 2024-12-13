<?php
require '_base.php';
//-----------------------------------------------------------------------------

if (is_post()) {
$email = req('email');
$code = req('code');
}

if($email & $code){

}
else{
    temp('info', 'Opps! Error Occured');
    redirect('/');
}

// ----------------------------------------------------------------------------
$_title = 'Detail';
include '_head.php';
?>

<p>Email : <?= $email ?></p>
<p>Code :<?= $code ?></p>

<?php
include '_foot.php';