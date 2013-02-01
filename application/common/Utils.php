<?php
date_default_timezone_set('Europe/London');

function br( $value )
{
	return $value."\n";
}

function array_push_assoc($array, $key, $value){
 $array[$key] = $value;
 return $array;
}


function para( $string, $classes="" )
{
	$extra_classes = (strlen($classes)>0) ? " class='$classes'" : "";
	return br( "<p".$extra_classes.">".$string."</p>" );
}

function constructURL( $filename, $queries = array() )
{
	$query_string = "";
	
	$i = 1;
    foreach( $queries as $field => $value ) 
	{
		$query_string.= $field."=".$value;
		if( $i != sizeof($queries) )
		{
			$query_string.="&amp;";
		}
		$i++;
	}
	
	return ( sizeof($queries)>0 ) ? $filename."?".$query_string : $filename;
}

function easylink( $name, $link="javascript:void(0);", $title="", $class="", $extras="" )
{
	$title = ($title=="") ? $name : $title;
	return "<a href='$link' title=\"$title\" class='$class' $extras>$name</a>";
}


if (!function_exists('json_decode')) {
    function json_decode($content, $assoc=false) {
        require_once 'JSON.php';
        if ($assoc) {
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        }
        else {
            $json = new Services_JSON;
        }
        return $json->decode($content);
    }
}

if (!function_exists('json_encode')) {
    function json_encode($content) {
        require_once 'JSON.php';
        $json = new Services_JSON;
        return $json->encode($content);
    }
}

function timenow()
{
	return date( "Y-m-d H:i:s" );
}

function minsToNiceTime( $mins )
{
	$fullDays    = floor( $mins/(60*24));
	$fullHours   = floor(($mins-($fullDays*60*24))/60);
	$fullMinutes = floor(($mins-($fullDays*60*24)-($fullHours*60)));

	$str = "";
	if( $fullDays > 0 )
	{
		$str.= "<span class='large'>" . $fullDays . "</span> day" . ( ($fullDays==1) ? "" : "s" ) . "&nbsp;&nbsp;";
	}
	if( $fullHours > 0 )
	{
		$str.= "<span class='large'>" . $fullHours . "</span> hour" . ( ($fullHours==1) ? "" : "s" ) . "&nbsp;&nbsp;";
	}
	if( $fullMinutes > 0 )
	{
		$str.= "<span class='large'>" . $fullMinutes . "</span> min" . ( ($fullMinutes==1) ? "" : "s" );
	}
	return $str;
} 

function pluralise( $val, $s_str, $p_str="", $pre=array() )
{
	$p_str = (strlen($p_str)==0) ? $s_str."s" : $p_str;
	
	$pre_string = ( sizeof( $pre ) == 2 ) ? ( ( ( intval($val)==1 ) ? $pre[0]: $pre[1] ) . " ") : "";
	
	return  $pre_string . $val . " " . ( ( intval($val)==1 ) ? $s_str: $p_str ) ;
}

?>