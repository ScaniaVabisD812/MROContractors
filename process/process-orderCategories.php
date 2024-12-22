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

function echoCategories($categories, $level = 0) 
{
    foreach($categories as $key => $value) 
    {
        echo '<option value="' . $key . '">' . str_repeat('&nbsp;&nbsp;', $level) . $value["Name"] . '</option>';
        if (!empty($value["Subcategories"])) 
        {
            echoCategories($value["Subcategories"], $level + 1);
        }
    }
}

function getCategoryName($categories, $categoryID)
{
    foreach($categories as $key => $value)
    {
        if($key == $categoryID)
        {
            return $value["Name"];
        }
        else
        {
            if($value["Subcategories"] != NULL)
            {
                $result = getCategoryName($value["Subcategories"], $categoryID);
                if($result != NULL)
                {
                    return $result;
                }
            }
        }
    }
    return NULL;
}

function getParentCategories($categories, $categoryID, $foundCategories, $pdo)
{
    $querystring = "SELECT ParentCategoryID FROM Categories WHERE CategoryID = :category";
    $stmt = $pdo->prepare($querystring);
    $stmt->bindParam(":category", $categoryID);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if($result["ParentCategoryID"] != NULL)
    {
        $foundCategories[] = $result["ParentCategoryID"];
        return getParentCategories($categories, $result["ParentCategoryID"], $foundCategories, $pdo);
    }
    else
    {
        return $foundCategories;
    }
}

function getChildCategories($categories, $categoryID, $foundArray)
{
    foreach($categories as $key => $value)
    {
        if($key == $categoryID)
        {
            if($value["Subcategories"] != NULL)
            {
                foreach($value["Subcategories"] as $subKey => $subValue)
                {
                    $foundArray[] = $subKey;
                    $foundArray = getChildCategories($categories, $subKey, $foundArray);
                }
            }
        }
        else
        {
            if($value["Subcategories"] != NULL)
            {
                $foundArray = getChildCategories($value["Subcategories"], $categoryID, $foundArray);
            }
        }
    }
    return $foundArray;
}
?>