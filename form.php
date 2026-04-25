<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form</title>
</head>
<body>

<form action="response.php" method="get">
    <select name="paper">
<?php
$xml = simplexml_load_file("https://wwwlab.webug.se/examples/XML/articleservice/papers/");

foreach ($xml->NEWSPAPER as $p) {
    echo "        <option value=\"" . $p['TYPE'] . "\">" . $p['NAME'] . "</option>\n";
}
?>
    </select>
    <input type="submit" value="Visa">
</form>

</body>
</html>