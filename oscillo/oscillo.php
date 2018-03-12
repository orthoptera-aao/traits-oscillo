<?php

function oscillo_info() {
  return(
    array(
      "oscillo" => array(
        "dependencies" => array("bioacoustica") //BioAcoustica provides wave file.
      )
    )
  );
}

function oscillo_init() {
  $init = array(
    "R" => array(
      "type" => "cmd",
      "required" => "required",
      "missing text" => "Oscillo requires R.",
      "version flag" => "--version"
    ),
    "seewave" => array( 
      "type" => "Rpackage",
      "required" => "required",
      "missing text" => "Oscillo requires the R seewave package.",
      "version flag" => "--quiet -e 'packageVersion(\"seewave\")'",
      "version line" => 1
    )
  );
  return ($init);
}

function oscillo_analyse($recording) {
  core_log("info", "oscillo", "OK!");
  $file = core_download("wav/".$recording["id"].".wav");
  print_r($file);
return array();

