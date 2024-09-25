<?php
// להבטיח שהתשובה מוחזרת בפורמט JSON עם קידוד UTF-8
header('Content-Type: application/json; charset=utf-8');

// יוצרים את התגובה בפורמט JSON הנדרש עבור ימות המשיח
$response = [
    "PlayTTS" => [
        "text" => "שלום עולם",   // הטקסט שיוקרא
        "language" => "hebrew"   // השפה היא עברית (hebrew)
    ]
];

// מחזירים את התשובה בפורמט JSON
echo json_encode($response);

?>
