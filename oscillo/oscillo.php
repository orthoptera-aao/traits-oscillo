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

function oscillo_prepare() {
  core_log("info", "oscillo", "Attempting to list oscillogram image files on analysis server.");
  exec("s3cmd ls s3://bioacoustica-analysis/oscillo/".$GLOBALS["modules"]["oscillo"]["git_hash"]."/", $output, $return_value);
  if ($return_value == 0) {
    if (count($output) == 0) {
      $GLOBALS["oscillo"]["oscillograms"] = array();
    } else {
      foreach ($output as $line) {
        $start = strrpos($line, "/");
        $GLOBALS["oscillo"]["oscillograms"][] = substr($line, $start + 1);
      }
    }
  core_log("info", "bioacoustica", count($GLOBALS["oscillo"]["oscillograms"])." oscillogram image files found.");
  }
  return(array());
}

function oscillo_analyse($recording) {
  $return = array();
  if (!in_array($recording["id"].".png", $GLOBALS["oscillo"]["oscillograms"])) {
    $file = core_download("wav/".$recording["id"].".wav");
    if ($file == NULL) {
      core_log("warning", "oscillo", "File was not available, skipping analysis.");
      return($return);
    }
    $return[$recording["id"].".wav"] = array(
      "file name" => $recording["id"].".wav",
      "local path" => "scratch/wav/",
      "save path" => NULL
    );
    core_log("info", "oscillo", "Attepting to create oscillogram for recording ".$recording["id"].".");
    exec("Rscript modules/traits-oscillo/oscillo/oscillo.R ".$recording["id"]." \"".$recording["taxon"]."\" scratch/wav/".$recording["id"].".wav", $output, $return_value);
    if ($return_value == 0) {
      $return[$recording["id"].".png"] = array(
        "file name" => $recording["id"].".png",
        "local path" => "modules/traits-oscillo/oscillo/",
        "save path" => "oscillo/".$GLOBALS["modules"]["oscillo"]["git_hash"]."/"
      );
    } else {
      switch ($return_value) {
        case 1:
          core_log("warning", "oscillo", "Recording ".$recording["id"].": Failed to read wave file: ".serialize($output));
          break;
        case 2:
          core_log("warning", "oscillo", "Recording ".$recording["id"].": Failed to normalise wave file: ".serialize($output));
          break;
        case 3:
          core_log("warning", "oscillo", "Recording ".$recording["id"].": Failed to plot oscillogram: ".serialize($output));
          break;
      }
    }
  }
  return($return);
}
