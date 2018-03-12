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
}

function oscillo_analyse($recording) {
  $return = array();
  $file = core_download("wav/".$recording["id"].".wav");
  $return[$recording["id"].".wav"] = array(
    "file name" => $recording["id"].".wav",
    "local path" => "scratch/wav/",
    "save path" => NULL
  );
  
  /*
  BELOW COPIED FROM OLD ANALYSIS
  
  if (file_exists("./oscillo/$recording_id".".png")) {
    print "Skipping file already plotted.".PHP_EOL;
  } else {
    $url = "https://ams3.digitaloceanspaces.com/bioacoustica-data-backup/recordings/$filename";
    system ("wget -O $filename $url");
    $format = strrchr(".", $filename);
    system("Rscript oscillo.R $recording_id '$species' $filename");
    system("rm $filename");
  }
  */
  
  if (!in_array($recording["id"].".png", $GLOBALS["oscillo"]["oscillograms"])) {
    core_log("info", "oscillo", "Attepting to create oscillogram for recording ".$recording["id"].".");
    echo "Rscript modules/oscillo/oscillo.R ".$recording["id"]." \"".$recording["taxon"]."\" ".$recording["id"].".wav";exit;
    exec("Rscript modules/oscillo/oscillo.R ".$recording["id"]." \"".$recording["taxon"]."\" ".$recording["id"].".wav", $output, $return_value);
    
  }
  
  return($return);
}
