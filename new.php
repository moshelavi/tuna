<?php
header('Content-Type: application/json; charset=utf-8');

// יוצרים את המבנה של התשובה ב-JSON לפי הפורמט של ימות המשיח
$response = [
    "PlayTTS" => [
        "text" => "שלום עולם",
        "language" => "hebrew"
    ]
];

// מחזירים את התשובה בפורמט JSON
echo json_encode($response);

?>
