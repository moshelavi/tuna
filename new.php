<?php
// קבל את מה שהמשתמש אמר
$user_input = $_REQUEST['user_input'];

// בדוק אם המשתמש אמר "שלום"
if (trim($user_input) == "שלום") {
    // המערכת תענה "עולם"
    echo "עולם";
} else {
    // תגובה אחרת
    echo "אני לא מבין";
}
?>
