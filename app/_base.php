<?php

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

// ============================================================================
// General Page Functions
// ============================================================================

// Is GET request?
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null)
{
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null)
{
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null)
{
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null)
{
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null)
{
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    } else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// Obtain uploaded file --> cast to object
function get_file($key)
{
    $f = $_FILES[$key] ?? null;

    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}

// Crop, resize and save photo
function save_photo($f, $folder, $width = 200, $height = 200) {
    $photo = uniqid() . '.jpg';
    
    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width, $height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return $photo;
}

// Is money?
function is_money($value) {
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}

// Is email?
function is_email($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

// Return local root path
function root($path = '') {
    return "$_SERVER[DOCUMENT_ROOT]/$path";
}

// Return base url (host + port)
function base($path = '') {
    return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/$path";
}

// ============================================================================
// HTML Helpers
// ============================================================================

// Encode HTML special characters
function encode($value)
{
    return htmlentities($value);
}

// Generate <input type='text'>
function html_text($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}

function html_password($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false)
{
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}
// Generate <input type="number"> field
// Render a number input with default value and attributes
// How to use: html_number('price', '100', ['min' => '0', 'max' => '1000', 'step' => '10', 'class' => 'number-input'], 'RM ');
function html_number($name, $value = '', $attrs = [], $prefix = '') {
    $attrs_str = '';
    foreach ($attrs as $k => $v) {
        $attrs_str .= " $k=\"" . htmlspecialchars($v) . '"';
    }
    
    $prefix_html = $prefix ? "<span class='input-prefix'>$prefix</span>" : '';
    
    return "$prefix_html<input type='number' name='$name' value='" . 
           htmlspecialchars($value) . "'$attrs_str>";
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate <input type='file'>
function html_file($key, $accept = '', $attr = '')
{
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

// Generate <input type='search'>
function html_search($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='search' id='$key' name='$key' value='$value' $attr>";
}

// Generate <textarea>
function html_textarea($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<textarea id='$key' name='$key' $attr>$value</textarea>";
}

// Generate table headers <th>
function table_headers($fields, $sort, $dir, $href = '')
{
    foreach ($fields as $k => $v) {
        if ($k === 'action') {
            echo "<th>$v</th>";
        } else {
            $d = 'asc'; // Default direction
            $c = '';    // Default class

            if ($k == $sort) {
                $d = $dir == 'asc' ? 'desc' : 'asc';
                $c = $dir;
            }

            echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v</a></th>";
        }
    }
}
// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = [];

// Generate <span class='err'>
function err($key)
{
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class='err'>$_err[$key]</span>";
    } else {
        echo '<span></span>';
    }
}

// ============================================================================
// Database Setups and Functions
// ============================================================================

// Global PDO object
// TODO
$_db = new PDO('mysql:dbname=gadgetwebdb', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

// Is unique?
function is_unique($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

// ============================================================================
// Security
// ============================================================================

// Global user object
$_member = $_SESSION['member'] ?? null;
$_admin = $_SESSION['admin'] ?? null;

// Login user
function login($member, $url = '/home.php') {
    $_SESSION['member'] = $member;
    redirect($url);
}

// Logout user
function logout($url = '/index.php') {
    unset($_SESSION['member']);
    redirect($url);
}

function adminlogin($admin, $url = '/adminHome.php') {
    $_SESSION['admin'] = $admin;
    redirect($url);
}

// Logout user
function adminlogout($url = '/adminLogin.php') {
    unset($_SESSION['admin']);
    redirect($url);
}

// Authorization
function auth(...$roles) {
    global $_user;
    if ($_user) {
        if ($roles) {
            if (in_array($_user->role, $roles)) {
                return; // OK
            }
        }
        else {
            return; // OK
        }
    }
    
    redirect('/home.php');
}

// ============================================================================
// Email Functions
// ============================================================================

// Demo Accounts:
// --------------
// AACS3173@gmail.com           npsg gzfd pnio aylm
// BAIT2173.email@gmail.com     ytwo bbon lrvw wclr
// liaw.casual@gmail.com        wtpa kjxr dfcb xkhg
// liawcv1@gmail.com            obyj shnv prpa kzvj

// Initialize and return mail object
function get_mail() {
    require_once 'lib/PHPMailer.php';
    require_once 'lib/SMTP.php';

    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = 'AACS3173@gmail.com';
    $m->Password = 'npsg gzfd pnio aylm';
    $m->CharSet = 'utf-8';
    $m->setFrom($m->Username, '😺 Admin');

    return $m;
}
function auto_id($idColumn, $tableName, $idPrefix, $pattern = '/(\d+)$/', $padLength = 5)
{
    global $_db;
    $stmt = $_db->query("SELECT $idColumn FROM $tableName ORDER BY $idColumn DESC LIMIT 1");
    $lastId = $stmt->fetchColumn();

    if (!$lastId) {
        return sprintf("%s%0{$padLength}d", $idPrefix, 1);
    }

    if (preg_match($pattern, $lastId, $matches)) {
        $lastIdNum = (int)$matches[1]; 
        $newIdNum = $lastIdNum + 1;
    } else {
        throw new Exception("Invalid ID format in the table.");
    }

    return sprintf("%s%0{$padLength}d", $idPrefix, $newIdNum);
}


// ============================================================================
// Global Constants and Variables
// ============================================================================

$_genders = [
    'F' => 'Female',
    'M' => 'Male',
];

$_status = [
    'A' => 'Active',
    'U' => 'Unactive',
];
