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
<?php
if (isset($_GET['paper']) && $_GET['paper'] !== "") {
    $paper = $_GET['paper'];
} else {
    $paper = "Evening_Edition";
}

$url = "https://wwwlab.webug.se/examples/XML/articleservice/articles/?paper=" . $paper;
$data = file_get_contents($url);