<?php
function export($data, $supplied_opts = array() )
{
		$default_opts = array('filename'=>'data-exporter', 'extension'=>'xls');
		extract( ArrayManipulator::copy_array($default_opts, $supplied_opts) );
		
		//Original PHP code by Chirp Internet: www.chirp.com.au
		function cleanData(&$str)
		{
			// escape tab characters
			$str = preg_replace("/\t/", "\\t", $str);

			// escape new lines
			$str = preg_replace("/\r?\n/", "\\n", $str);

			// convert 't' and 'f' to boolean values
			if($str == 't') $str = 'TRUE';
			if($str == 'f') $str = 'FALSE';

			// force certain number/date formats to be imported as strings
			if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
			  $str = "'$str";
			}

			// escape fields that include double quotes
			if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
		
		}

		$filename .= date('Ymd'). '.'. $extension;
		
		header("Content-Disposition: attachment; filename=\"$filename\"");
		
		if($extension == 'xls')
		{
			//@credits: http://www.the-art-of-web.com/php/dataexport/
			header("Content-Type: application/vnd.ms-excel");
			$flag = false;
			foreach($data as $row)
			{
				if(!$flag)
				{
					//display field/column names as first row
					echo implode("\t", array_keys($row)) . "\r\n";
					$flag = true;
				}
			
				array_walk($row, 'cleanData');
				echo implode("\t", array_values($row)) . "\r\n"; 
			}
		}
		
		else if($extension == 'csv')
		{
			//@credits: http://webtricksandtreats.com/export-to-csv-php/
			header("Content-Type: application/csv");
			
			/** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
			$f = fopen('php://memory', 'w');
			
			$flag = false;
			foreach($data as $row)
			{
				if(!$flag)
				{
					//display field/column names as first row
					fputcsv($f, array_keys($row));
					$flag = true;
				}
			
				array_walk($row, 'cleanData');
				fputcsv($f, array_values($row));
			}
			
			/** rewrind the "file" with the csv lines **/
			fseek($f, 0);
			fpassthru($f);
		}
}