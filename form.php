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

foreach ($xml->paper as $p) {
    echo "        <option value=\"" . $p['type'] . "\">" . $p->name . "</option>\n";
}
?>
    </select>
    <input type="submit" value="Visa">
</form>

</body>
</html>