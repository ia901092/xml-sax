<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Artiklar</title>
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Verdana, sans-serif;
        }
        table.outer {
            border: 2px solid #5a3e2b;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #f5e6d3;
        }
        table.outer > tbody > tr > td {
            border: 1px solid #5a3e2b;
            padding: 10px;
            vertical-align: top;
        }
        table.inner {
            border: 1px solid #5a3e2b;
            border-collapse: collapse;
            width: 100%;
            background-color: #fffdf8;
        }
        table.inner > tbody > tr > td {
            border: 1px solid #5a3e2b;
            padding: 8px;
            vertical-align: top;
        }
        td.news { background-color: #dff0d8; }
        td.review { background-color: #fdf2d0; }
        .story p {
            border-left: 4px solid #5a3e2b;
            padding: 6px 10px;
            margin: 6px 0;
            background-color: #ffffff;
            box-shadow: 2px 2px 5px #ccc;
        }
        h3 {
            font-family: "Courier New", monospace;
            font-size: 16px;
            font-weight: bold;
        }
        p {
            font-family: "Courier New", monospace;
            font-size: 12px;
            font-weight: normal;
        }
    </style>
</head>
<body>

<?php
// default Evening_Edition om inget valts
if (isset($_GET['paper']) && $_GET['paper'] !== "") {
    $paper = $_GET['paper'];
} else {
    $paper = "Evening_Edition";
}

$url = "https://wwwlab.webug.se/examples/XML/articleservice/articles/?paper=" . $paper;

// globala variabler
$previous = "";
$newspaperAttrs = array();
$articles = array();
$currentArticle = -1;

// hitta attribut oavsett case
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

$data = file_get_contents($url);

$parser = xml_parser_create();
xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 1);
xml_set_element_handler($parser, "start", "stop");
xml_set_character_data_handler($parser, "text");
xml_parse($parser, $data, true);
xml_parser_free($parser);

// yttre tabell = newspaper
echo "<table class=\"outer\">\n";
echo "    <tbody>\n";

// rad 1: newspaper attribut (explicit)
echo "        <tr>\n";
echo "            <td>";
echo "Edition: " . htmlspecialchars(getAttr($newspaperAttrs, "edition"));
echo " | Subscribers: " . htmlspecialchars(getAttr($newspaperAttrs, "subscribers"));
echo "</td>\n";
echo "        </tr>\n";

// rad 2: inre tabell inuti yttre (nestled tables)
echo "        <tr>\n";
echo "            <td>\n";
echo "                <table class=\"inner\">\n";
echo "                    <tbody>\n";

// inre rad 1: article attribut (explicit) - en kolumn per artikel
echo "                        <tr>\n";
foreach ($articles as $a) {
    $class = htmlspecialchars(strtolower(getAttr($a['attrs'], "type")));
    echo "                            <td class=\"" . $class . "\">";
    echo "Id: " . htmlspecialchars(getAttr($a['attrs'], "id")) . "<br>";
    echo "Date: " . htmlspecialchars(getAttr($a['attrs'], "date")) . "<br>";
    echo "Type: " . htmlspecialchars(getAttr($a['attrs'], "type"));
    echo "</td>\n";
}
echo "                        </tr>\n";

// inre rad 2: story - en kolumn per artikel
echo "                        <tr>\n";
foreach ($articles as $a) {
    $class = htmlspecialchars(strtolower(getAttr($a['attrs'], "type")));
    echo "                            <td class=\"" . $class . "\">\n";
    echo "                                <div class=\"story\">\n";
    if ($a['heading'] !== "") {
        echo "                                    <h3>" . htmlspecialchars($a['heading']) . "</h3>\n";
    }
    foreach ($a['texts'] as $t) {
        echo "                                    <p>" . htmlspecialchars($t) . "</p>\n";
    }
    echo "                                </div>\n";
    echo "                            </td>\n";
}
echo "                        </tr>\n";

echo "                    </tbody>\n";
echo "                </table>\n"; // stänger inner
echo "            </td>\n";
echo "        </tr>\n";
echo "    </tbody>\n";
echo "</table>\n"; // stänger outer
?>

<p><a href="form.php">Tillbaka</a></p>

</body>
</html>