<?php
require '_base.php';
// ----------------------------------------------------------------------------

if (is_get()) {
    $id = req('id');

    // TODO
    $stm = $_db->prepare('SELECT * FROM member WHERE id = ?');
    $stm->execute([$id]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('/');
    }

   $member_name = $s->member_name;
    $member_gender = $s->member_gender;
 

//shortcut way but not recommend
 //   extract((array)$s);
}

if (is_post()) {
    // Input
    $member_id         = req('id'); // <-- From URL
    $member_name       = req('name');
    $member_gender     = req('gender');
    

    // Validate id <-- NO NEED
    
    // Validate name
    if ($member_name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($member_name) > 100) {
        $_err['name'] = 'Maximum length 100';
    }

    // Validate gender
    if ($member_gender == '') {
        $_err['gender'] = 'Required';
    }
    else if (!array_key_exists($member_gender, $member_genders)) {
        $_err['name'] = 'Invalid value';
    }

    // Output
    if (!$_err) {
        $stm = $_db->prepare('UPDATE member
                              SET name = ?, gender = ?
                              WHERE id = ?');
        $stm->execute([$name, $gender, $id]);

        temp('info', 'Record updated');
        redirect('/');
    }
}

// ----------------------------------------------------------------------------
$_title = 'Update';
include '_head.php';
?>

<form method="post" class="form">
    <label for="id">Id</label>
    <b><?= $member_id ?></b>
    <?= err('id') ?>

    <label for="member_name">Name</label>
    <?= html_text('member_name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label>Gender</label>
    <?= html_radios('member_gender', $member_genders) ?>
    <?= err('gender') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '_foot.php';