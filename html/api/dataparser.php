<?php

include( dirname(__FILE__) . "/../../app/__init.php" );
include( dirname(__FILE__) . "/../../app/GreaseRatEvent.model.php" );

function validateExists( $dst, $name )
{
    $values = array_keys( $dst );
    return in_array( $name, $values );
}

function validateEmpty( $dst, $name )
{
    return !empty($dst[$name]);
}

function validateInt( $dst, $name )
{
    return is_numeric($dst[$name]);
}

function validateDate( $dst, $name )
{
    if( !validateExists( $dst, $name ) )
    {
        return false;
    }

    return true;
}

function validateValue( $validateConfig, $validateFn, $post)
{
    $validateResult = [];

    foreach ($validateConfig[$validateFn] as $key => $value)
    {
        if( !call_user_func($validateFn, $post, $value))
        {
            array_push($validateResult, $value);
        }
    }

    if( count( $validateResult ) > 0 )
    {
        return showErrors($validateFn, $validateResult);
    }
}

function showErrors( $name, $data )
{
    echo json_encode((Object)[
        "err" => $name,
        "data" => $data
    ]);
    die();
}

function validatePostData( $post )
{
    $valueNames = array(
        "repairPost",
        "customer",
        "typeOfRepair",
        "avtoModel",
        "mileage",
        "vin",
        "startdatetime",
        "enddatetime"
    );

    $validateConfig = array(
        "validateExists" => array(
            "repairPost",
            "customer",
            "typeOfRepair",
            "avtoModel",
            "mileage",
            "startdatetime",
            "enddatetime"
        ),
        "validateEmpty" => array(
            "customer",
            "mileage",
            "startdatetime",
            "enddatetime"
        ),
        "validateDate" => array(
            "startdatetime",
            "enddatetime"
        ),
        "validateInt" => array(
            "repairPost",
            "typeOfRepair",
            "avtoModel",
            "mileage",
        )
    );

    $filteredPost = [];
    foreach ($post as $key => $value)
    {
        if( in_array($key, $valueNames) )
        {
            $filteredPost[$key] = $value;
        }
    }

    validateValue($validateConfig, "validateExists", $filteredPost);
    validateValue($validateConfig, "validateEmpty", $filteredPost);
    validateValue($validateConfig, "validateInt", $filteredPost);
    validateValue($validateConfig, "validateDate", $filteredPost);

    $newEvent = new GreaseRatEvent;
    $newEvent->repairPost    = intval($filteredPost["repairPost"]);
    $newEvent->customer      = $filteredPost["customer"];
    $newEvent->typeOfRepair  = intval($filteredPost["typeOfRepair"]);
    $newEvent->avtoModel     = intval($filteredPost["avtoModel"]);
    $newEvent->mileage       = intval($filteredPost["mileage"]);
    $newEvent->vin           = $filteredPost["vin"];
    $newEvent->startdatetime = $filteredPost["startdatetime"];
    $newEvent->enddatetime    = $filteredPost["enddatetime"];
    $newEvent->save();

    echo json_encode($newEvent);
    die();
}

if( $_POST )
{
    validatePostData( $_POST );
}


