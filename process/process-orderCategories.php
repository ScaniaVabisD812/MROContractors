<?php
$pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);

$querystring = "SELECT * FROM Categories;";
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->prepare($querystring);
$stmt->execute();
$queryResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = array();
$unsearchedCategories = array();

foreach($queryResult as $row)
{
    if($row["ParentCategoryID"] == NULL)
    {
        $categories[$row["CategoryID"]] = array("Name" => $row["Name"], "Subcategories" => array());
    }
    else
    {
        $unsearchedCategories[] = $row;
    }
}

function search(&$array, $searchedID, $insertID, $insertName)
{
    foreach($array as $key => $value)
    {
        if($key == $searchedID)
        {
            $array[$key]["Subcategories"][$insertID] = array("Name" => $insertName, "Subcategories" => array());
            return $array;
        }
        else
        {
            if($value["Subcategories"] != NULL)
            {
                $result = search($array[$key]["Subcategories"], $searchedID, $insertID, $insertName);
                if($result != NULL)
                {
                    return $result;
                }
            }
        }
    }
    return NULL;
}

while(count($unsearchedCategories) > 0)
{
    foreach($unsearchedCategories as $key => $value)
    {
        $result = search($categories, $value["ParentCategoryID"], $value["CategoryID"], $value["Name"]);
        if($result != NULL)
        {
            unset($unsearchedCategories[$key]);
        }
    }
}

/* function echoCategories($categories, $level = 0) 
{
    foreach($categories as $key => $value) 
    {
        if (!empty($value["Subcategories"])) 
        {
            echo '<optgroup label="' . str_repeat('-', $level) . ' ' . $value["Name"] . '">';
            echoCategories($value["Subcategories"], $level + 1);
            echo '</optgroup>';
        } else 
        {
            echo '<option value="' . $key . '">' . str_repeat('-', $level) . ' ' . $value["Name"] . '</option>';
        }
    }
}
*/

function echoCategories($categories, $level = 0) {
    foreach($categories as $key => $value) {
        echo '<option value="' . $key . '">' . str_repeat('&nbsp;&nbsp;', $level) . $value["Name"] . '</option>';
        if (!empty($value["Subcategories"])) {
            echoCategories($value["Subcategories"], $level + 1);
        }
    }
}

?>