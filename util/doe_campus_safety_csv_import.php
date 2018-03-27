<?php
	
	//USAGE: php doe_campus_safety_csv_import.php report_file_name.csv
	//Report must be a CSV (expects the Excel format provided by the DOE in a csv format)
	//Report file name should be set to file_type-yearrange.csv (i.e. oc_vawa-121416.csv for an On Campus VAWA Report) -- years should be last 2 numbers only 
	//allowed file types are set in $allowedFileTypes
	//VAWA reports are expected to be a multi year range, while all others should be single years
	//Any existing data in the database for the given report type and year will be overwritten by the current input file to prevent duplicate data
	
	require_once("../config/db_conf.php");
	
	//set the allowed file types to ensure an ingestable file is given
	$allowedFileTypes = array("oc_vawa", "rh_vawa", "oc_arrest", "rh_arrest", "oc_crime", "rh_crime");
	
	$inputFile = false;
	
	if (isset($argv[1])) {
		//verify the file at least looks like a csv
		if (strpos($argv[1], ".csv") !== false) {
			$inputFile = $argv[1];
		}
	}
	
	if ($inputFile === false) {
		echo "CSV file name not specified (or file isn't a CSV). Exiting." . PHP_EOL;
		exit;
	}
	
	//determine what kind of csv was input along with its date range
	$baseFileName = pathinfo($inputFile, PATHINFO_FILENAME);
	
	$explodedFileName = explode("-", $baseFileName);
	
	if (in_array($explodedFileName[0], $allowedFileTypes) === false) {
		echo "Invalid file name input. Valid name format is file_type-yearrange.csv. Allowed file types: " . PHP_EOL;
		print_r($allowedFileTypes);
		echo "Exiting." . PHP_EOL;
		exit;
	}
	
	//set the report type
	$reportType = $explodedFileName[0];
	
	if (strpos($reportType, "vawa") !== false || strpos($reportType, "arrest") !== false || strpos($reportType, "crime") !== false) {
		//this should be a multi year date range
		if (strlen($explodedFileName[1]) == 12) {
			$reportYear = str_split($explodedFileName[1], 4);
		} else {
			//invalid date range
			echo "Invalid date range. Input file should be a 3 year range based on report type, which should have a 3 year range. Exiting." . PHP_EOL;
			exit;
		}
	} else {
		//this should be a single year date range
		if (strlen($explodedFileName[1]) == 4) {
			$reportYear = array($explodedFileName[1]);
		} else {
			//invalid date range
			echo "Invalid date range. Input file should have a single year. Exiting." . PHP_EOL;
			exit;
		}
	}
	
	$q = "SELECT id,name from schools";
	
	if (!$result = $dbc->query($q)) {
		echo "Failed to load schools." . PHP_EOL;
		echo $dbc->error . PHP_EOL;
		echo "Exiting." . PHP_EOL;
		exit;
	}
	
	$schools = array();
	
	//create the default stats array based on the report type
	$stats = array();
	
	if (strpos($reportType, "vawa") !== false) {
		foreach ($reportYear as $year) {
			$stats[$year] = array("domestic_violence" => 0, "dating_violence" => 0, "stalking" => 0);
		}
	} elseif (strpos($reportType, "arrest") !== false) {
		foreach ($reportYear as $year) {
			$stats[$year] = array("weapon" => 0, "drug" => 0, "liquor" => 0);
		}
	} elseif (strpos($reportType, "crime") !== false) {
		foreach ($reportYear as $year) {
			$stats[$year] = array("murder" => 0, "neg_manslaughter" => 0, "rape" => 0, "fondling" => 0, "incest" => 0, "stat_rape" => 0, "robbery" => 0, "agg_assault" => 0, "vehicle_theft" => 0, "arson" => 0);
		}
	}

	while ($row = $result->fetch_object()) {
		$schools[$row->name]["uid"] = $row->id;
		
		//set default stats array
		$schools[$row->name]["stats"] = $stats;
	}

	$handle = fopen("../data/".$inputFile, "r");
	
	$header_skipped = false;
	
	$unknown_schools_count = 0;
	$unknown_schools = array();
	
	while (($data = fgetcsv($handle)) !== FALSE) {
		if (!$header_skipped) {
			$header_skipped = true;
			continue;
		}
		
		//skip this line if we don't know about the school
		if (!isset($schools[$data[1]])) {
			continue;
		}
		
		if (strpos($reportType, "vawa") !== false) {
			$columns = array($reportYear[0] => array("12", "13", "14"), $reportYear[1] => array("15", "16", "17"), $reportYear[2] => array("18", "19", "20"));
			
			foreach ($columns as $year => $range) {
				if (is_numeric($data[$range[0]])) {
					$schools[$data[1]]["stats"][$year]["domestic_violence"] += $data[$range[0]];
				}
				if (is_numeric($data[$range[1]])) {
					$schools[$data[1]]["stats"][$year]["dating_violence"] += $data[$range[1]];
				}
				if (is_numeric($data[$range[2]])) {
					$schools[$data[1]]["stats"][$year]["stalking"] += $data[$range[2]];
				}
			}
		} elseif (strpos($reportType, "arrest") !== false) {
			$columns = array($reportYear[0] => array("12", "13", "14"), $reportYear[1] => array("15", "16", "17"), $reportYear[2] => array("18", "19", "20"));
			
			foreach ($columns as $year => $range) {
				if (is_numeric($data[$range[0]])) {
					$schools[$data[1]]["stats"][$year]["weapon"] += $data[$range[0]];
				}
				if (is_numeric($data[$range[1]])) {
					$schools[$data[1]]["stats"][$year]["drug"] += $data[$range[1]];
				}
				if (is_numeric($data[$range[2]])) {
					$schools[$data[1]]["stats"][$year]["liquor"] += $data[$range[2]];
				}
			}
		} elseif (strpos($reportType, "crime") !== false) {
			$columns = array($reportYear[0] => array("12", "13", "14", "15", "16", "17", "18", "19", "20", "21"), $reportYear[1] => array("22", "23", "24", "25", "26", "27", "28", "29", "30", "31"), $reportYear[2] => array("32", "33", "34", "35", "36", "37", "38", "39", "40", "41"));
			
			foreach ($columns as $year => $range) {
				if (is_numeric($data[$range[0]])) {
					$schools[$data[1]]["stats"][$year]["murder"] += $data[$range[0]];
				}
				if (is_numeric($data[$range[1]])) {
					$schools[$data[1]]["stats"][$year]["neg_manslaughter"] += $data[$range[1]];
				}
				if (is_numeric($data[$range[2]])) {
					$schools[$data[1]]["stats"][$year]["rape"] += $data[$range[2]];
				}
				if (is_numeric($data[$range[3]])) {
					$schools[$data[1]]["stats"][$year]["fondling"] += $data[$range[3]];
				}
				if (is_numeric($data[$range[4]])) {
					$schools[$data[1]]["stats"][$year]["incest"] += $data[$range[4]];
				}
				if (is_numeric($data[$range[5]])) {
					$schools[$data[1]]["stats"][$year]["stat_rape"] += $data[$range[5]];
				}
				if (is_numeric($data[$range[6]])) {
					$schools[$data[1]]["stats"][$year]["robbery"] += $data[$range[6]];
				}
				if (is_numeric($data[$range[7]])) {
					$schools[$data[1]]["stats"][$year]["agg_assault"] += $data[$range[7]];
				}
				if (is_numeric($data[$range[8]])) {
					$schools[$data[1]]["stats"][$year]["vehicle_theft"] += $data[$range[8]];
				}
				if (is_numeric($data[$range[9]])) {
					$schools[$data[1]]["stats"][$year]["arson"] += $data[$range[9]];
				}
			}
		}

	}
	
	//get the current stats from the db to prevent duplicate data from being inserted
	$q = "SELECT id,schoolsId,year,type from annualcrimestats";
	
	if (!$result = $dbc->query($q)) {
		echo "Failed to load schools." . PHP_EOL;
		echo $dbc->error . PHP_EOL;
		exit;
	}
	
	$existingStats = array();
	
	while ($row = $result->fetch_object()) {
		$existingStats[$row->schoolsId][$row->type][$row->year] = array("id" => $row->id);
	}

	foreach ($schools as $school) {
		foreach ($school['stats'] as $year => $stat) {
			
			if (isset($existingStats[$school["uid"]][$reportType][$year])) {
				$statId = $existingStats[$school["uid"]][$reportType][$year];
				$q = "UPDATE annualcrimestats SET data='".$dbc->escape_string(json_encode($stat))."' where id = '".$statId."'";
			} else {
				$q = "INSERT INTO annualcrimestats (schoolsId, type, year, data) VALUES ('".$school["uid"]."', '".$reportType."', '".$year."', '".$dbc->escape_string(json_encode($stat))."')";	
			}
	
			if (!$result = $dbc->query($q)) {
				echo "Failed to insert data." . PHP_EOL;
				echo $dbc->error . PHP_EOL;
				continue;
			}
		}
	}
	
	mysqli_close($dbc);
?>