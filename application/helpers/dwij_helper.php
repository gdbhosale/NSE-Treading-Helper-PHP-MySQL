<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('startsWith')) {
	function startsWith($haystack, $needle) {
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}
}

if ( ! function_exists('endsWith')) {
	function endsWith($haystack, $needle) {
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
	}
}

if (!function_exists('tsFormat')) {
	function tsFormat($str, $format) {
		$ts = strtotime($str);
		return gmdate($format, $ts);
	}
}

if (!function_exists('shortenUrl')) {
	function shortenUrl($longUrl) {
		$apiKey = 'AIzaSyDvaUg89uMNUQ3CSkUpio6dD0IudZ2ZWmQ';

		$postData = array('longUrl' => $longUrl, 'key' => $apiKey);
		$jsonData = json_encode($postData);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		
		$page = curl_exec($ch);

		$dat = json_decode($page);
		if(isset($dat->id)) {
			$page = $dat->id;
			$curl_error_str = curl_error($ch);
			log_message("debug", "shortenUrl: ".$page);
			if($curl_error_str != NULL || $curl_error_str != "") {
				$page = $curl_error_str;
				//log_message("error", "shortenUrl: ".$page);
			}
			curl_close($ch);
			return $page;
		} else {
			return $longUrl;
		}
	}
}

function getComingDayOfWeek($dow) {
	//echo $dow;
	$dowArr = array( "SUNDAY", "MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY");
	//0 for Sunday, 6 for Saturday
	$cntDateT = time();
	$reqDay = array_search($dow, $dowArr);
	$cntDay = date("w", $cntDateT);

	$cntDate = date("m/d/Y");

	$diff = 0;
	if($reqDay == $cntDay) {
		$diff = 0;
	} else if($reqDay > $cntDay) {
		$diff = $reqDay - $cntDay;
	} else {
		$diff = 7 - ($cntDay - $reqDay);
	}
	//echo "Add ".($diff)." Days";
	$tomorrow = date('Y-m-d', strtotime($cntDate . " +".$diff." days"));
	return $tomorrow;
}

function process_perm($perms) {
	if (($perms & 0xC000) == 0xC000) {
	    // Socket
	    $info = 's';
	} elseif (($perms & 0xA000) == 0xA000) {
	    // Symbolic Link
	    $info = 'l';
	} elseif (($perms & 0x8000) == 0x8000) {
	    // Regular
	    $info = '-';
	} elseif (($perms & 0x6000) == 0x6000) {
	    // Block special
	    $info = 'b';
	} elseif (($perms & 0x4000) == 0x4000) {
	    // Directory
	    $info = 'd';
	} elseif (($perms & 0x2000) == 0x2000) {
	    // Character special
	    $info = 'c';
	} elseif (($perms & 0x1000) == 0x1000) {
	    // FIFO pipe
	    $info = 'p';
	} else {
	    // Unknown
	    $info = 'u';
	}

	// Owner
	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ?
	            (($perms & 0x0800) ? 's' : 'x' ) :
	            (($perms & 0x0800) ? 'S' : '-'));

	// Group
	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ?
	            (($perms & 0x0400) ? 's' : 'x' ) :
	            (($perms & 0x0400) ? 'S' : '-'));

	// World
	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ?
	            (($perms & 0x0200) ? 't' : 'x' ) :
	            (($perms & 0x0200) ? 'T' : '-'));

	return $info;
}

function delete_file($file) {
	if(file_exists($file)) {
		log_message("debug", "Deleting '".$file."'");
		if(chmod($file, 0777)) {
			return unlink($file);
		} else {
			log_message("error", "Cannot change file '".$file."' permmissions for deletion");
			return FALSE;
		}
	} else {
		return TRUE;
	}
}

function emptyFolder($folder) {
    $files = glob($folder."/*");
    foreach($files as $file) {
        if(is_file($file)) {
            delete_file($file);
        }
    }
}

function processSymbol($symb) {
    $symb = str_replace("&", "_", $symb);
    $symb = str_replace("-", "_", $symb);
    $symb = str_replace(",", "_", $symb);
    $symb = strtoupper($symb);

    return trim($symb);
}

function trimCSVRow($row) {
	$rowN = array();
    foreach ($row as $key => $value) {
    	if(trim($key) != "") {
    		if(trim($key) == "OPEN_INT*") {
    			$key = "OPEN_INT";
    		}
    		$rowN[trim($key)] = trim($value);
    	}
    }
    return $rowN;
}

function unzipFile($zipFile, $folder) {
    $zip = new ZipArchive;
    $error = "";
    if($zip->open($zipFile) != "true") {
        $error .= "Error :- Unable to open the Zip File: ".$zipFile."\n<br>";
        log_message("error", "Unable to open the Zip File: ".$zipFile);
    }
    //Empty temp
    //emptyFolder($folder);
    // Extract Zip File
    $zip->extractTo($folder);
    $zip->close();
    return $error;
}

function downloadFile($src, $dest) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $src);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt($ch, CURLOPT_FILE, $dest);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13');
    $page = curl_exec($ch);
    $curl_error_str = curl_error($ch);
    curl_close($ch);

    $r = array("page" => $page, "curl_error_str" => $curl_error_str);
    return $r;
}

// http://davidwalsh.name/php-calendar
function draw_calendar($month, $year, $report_days) {
	$CI =& get_instance();
	$base_url = $CI->config->item("base_url");

	$cntDate = intval(date("d"));
	$cntMonth = intval(date("m"));
	$cntYearY = intval(date("Y"));
	$cntYear = intval(date("y"));

	$monthT = $month;
	if($month < 10) {
		$monthT = "0".$month;
	}
	$yearT = $year;
	if($year >= 2000) {
		$yearT = $year - ((int)($year / 100) * 100);
	}

	$cntTime = strtotime("".$cntYearY."-".$cntMonth."-".$cntDate);

	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

	/* table headings */
	//$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$headings = array('S','M','T','W','T','F','S');
	$monthsArr = array(
	    'January',
	    'February',
	    'March',
	    'April',
	    'May',
	    'June',
	    'July ',
	    'August',
	    'September',
	    'October',
	    'November',
	    'December',
	    );
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head monthS" colspan="7">'.$monthsArr[$month-1].' <a href="'.$base_url.'/calendar/update_db_monthwise?month='.$monthT.'&year='.$yearT.'" class="btn btn-success btn-xs pull-right"><i class="fa fa-download"></i></a></td></tr>';
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">', $headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np"> </td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):

		// trim date
		$dateT = $list_day;
		if($list_day < 10) {
			$dateT = "0".$list_day;
		}
		$dateTime = strtotime("".$year."-".$monthT."-".$dateT);
		// process data load job
		$is_data_loaded = false;
		$data_load_success = true;

		$datt = array();

		if(isset($report_days[$year."-".$monthT."-".$dateT])) {
			$is_data_loaded = true;
			$datt = $report_days[$year."-".$monthT."-".$dateT];
			$datt['data'] = json_decode($datt['data']);
			if($datt['status'] == "SUCCESS") {
				$data_load_success = true;
			} else {
				$data_load_success = false;
			}
		}

		$classS = "";
		$contentS = "";
		// compare dates
		//echo "Now: ".("".$cntYearY."-".$cntMonth."-".$cntDate)." Date: ".("".$year."-".$monthT."-".$dateT);

		//echo "Now: ".$cntTime." Date: ".$dateTime;
		if($cntTime > $dateTime) {
			if($is_data_loaded) {
				if($data_load_success) {
					$classS = "green";
					$contentS = "Companies: ".$datt['tot_com_load']."<br>New Com.: ".$datt['new_com_load']."<br><a class='btn btn-primary btn-xs'  href='".$base_url."/calendar/report_datewise?date=".$dateT."-".$monthT."-".$yearT."'>Report</a><a class='btn btn-warning btn-xs' style='margin-top:5px;' href='".$base_url."/calendar/update_db_datewise?date=".$dateT."-".$monthT."-".$yearT."'>Download Again</a>";
					$contentS .= "<ol>";
					foreach ($datt['data']->new_companies as $value) {
						$contentS .= "<li>".$value."</li>";
					}
					$contentS .= "</ol>";
				} else {
					$classS = "red";
					$contentS = "<a class='btn btn-primary btn-xs' style='margin-top:5px;' href='".$base_url."/calendar/report_datewise?date=".$dateT."-".$monthT."-".$yearT."'>Report</a><a class='btn btn-danger btn-xs' style='margin-top:5px;' href='".$base_url."/calendar/update_db_datewise?date=".$dateT."-".$monthT."-".$yearT."'>Retry</a>";
				}
			} else {
				$classS = "";
				$contentS = "<a class='btn btn-primary btn-xs' href='".$base_url."/calendar/update_db_datewise?date=".$dateT."-".$monthT."-".$yearT."'>Download</a>";
			}
		} else {
			$classS = "white";
			$contentS = "Cannot download future data.";
		}
		$calendar.= '<td class="calendar-day '.$classS.'">';

		/* add in the day number */
		$calendar.= '<button class="day-number" data-toggle="popover" data-placement="top" title="Date: '.$list_day." ".$monthsArr[$month-1]." ".$year.'" data-content="'.$contentS.'">'.$list_day.'</button>';

		/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
		//$calendar.= str_repeat('<p> </p>',2);
			
		$calendar.= '</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np"> </td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';
	
	/* all done, return result */
	return $calendar;
}