<?php
require '_base.php';
// ----------------------------------------------------------------------------
if (!empty($_SESSION["admin"])) {
    redirect('adminHome.php'); 
}

if (is_post()) {
    // Input
    $admin_id         = req('admin_id');
    $admin_password     = req('admin_password');
    $admin_email     = req('admin_email');

    // Output
    if (!$_err) {
        $stm = $_db->prepare('SELECT * FROM admin WHERE admin_email = ?');
        $stm->execute([$admin_email]);
        $admin = $stm->fetch();

        if ($admin) {

            //$admin_EncrptPassword = sha1($admin_password);
            // Verify password
            if ($admin_password === $admin->admin_password) {
                // Login successful
                temp('info', $admin->admin_id.' Login successful');
                adminlogin($admin);
            } else {
                $_err['admin_password'] = 'Incorrect password';
            }
        } else {
            $_err['admin_email'] = 'Email not registered';
        }
    }
}

// ----------------------------------------------------------------------------
$_title = 'Admin Login';
include '_head.php';
?>

<form method="post" class="form">

    <label for="admin_email">Email</label>
    <?= html_text('admin_email', 'maxlength="40"') ?>
    <?= err('admin_email') ?>

    <label for="admin_password">Password</label>
    <?= html_password('admin_password', 'maxlength="100"') ?>
    <?= err('admin_password') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '_foot.php';