<?php
// קובץ טיונר טלפוני לדוגמה עבור מערכת ימות המשיח

// הגדרת כותרות התגובה כקובץ XML
header('Content-Type: text/xml; charset=UTF-8');

// הפונקציה שמחזירה את ה-XML למערכת ימות המשיח
function respond($response) {
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<Response>';
    echo $response;
    echo '</Response>';
}

// בדיקה אם התקבל קלט כלשהו מהמקש שהמשתמש הקיש
if (isset($_GET['Digits'])) {
    $digits = $_GET['Digits']; // קבלת המקש שהוקש

    // בניית התגובה בהתאם למקש שהמשתמש לחץ
    switch ($digits) {
        case '1':
            respond('<Say>לחצת על המקש 1. תודה!</Say>');
            break;
        case '2':
            respond('<Say>לחצת על המקש 2. כל הכבוד!</Say>');
            break;
        case '3':
            respond('<Say>לחצת על המקש 3. יופי!</Say>');
            break;
        default:
            respond('<Say>הבחירה שלך לא ידועה. נסה שוב.</Say>');
            break;
    }
} else {
    // הודעה במקרה שלא הוקש מקש
    respond('<Gather numDigits="1">
                <Say>נא ללחוץ על מקש 1 עד 3 כדי לבחור.</Say>
            </Gather>');
}
?>
