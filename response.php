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
$previous = "";
$newspaperAttrs = array();
$articles = array();
$currentArticle = -1;

function getAttr($attrs, $key) {
    foreach ($attrs as $k => $v) {
        if (strtolower($k) == strtolower($key)) {
            return $v;
        }
    }
    return "";
}

function start($parser, $name, $attrs) {
    global $newspaperAttrs, $articles, $currentArticle, $previous;
    $name = strtoupper($name);

    if ($name == "NEWSPAPER") {
        $newspaperAttrs = $attrs;
    }
    if ($name == "ARTICLE") {
        $currentArticle++;
        $articles[$currentArticle] = array(
            'attrs' => $attrs,
            'heading' => '',
            'texts' => array()
        );
    }

    $previous = $name;
}

function stop($parser, $name) {
    global $previous;
    $previous = "";
}

function text($parser, $data) {
    global $articles, $currentArticle, $previous;

    if ($previous == "COMMENT") {
        return;
    }
    if ($currentArticle < 0) {
        return;
    }

    $data = trim($data);
    if ($data === "") {
        return;
    }

    if ($previous == "HEADING") {
        $articles[$currentArticle]['heading'] .= $data;
    } elseif ($previous == "TEXT") {
        $articles[$currentArticle]['texts'][] = $data;
    }
}

$parser = xml_parser_create();
xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 1);
xml_set_element_handler($parser, "start", "stop");
xml_set_character_data_handler($parser, "text");
xml_parse($parser, $data, true);
xml_parser_free($parser);