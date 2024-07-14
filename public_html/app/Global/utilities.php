<?php
    use Illuminate\Http\Request;
    use App\Http\Requests;
    use Illuminate\Support\Facades\Storage;
    

    function relativeTime($time) {
        $time = strtotime($time);
        $d[0] = array(1,"second");
        $d[1] = array(60,"minute");
        $d[2] = array(3600,"hour");
        $d[3] = array(86400,"day");
        $d[4] = array(604800,"week");
        $d[5] = array(2592000,"month");
        $d[6] = array(31104000,"year");

        $w = array();

        $return = "";
        $now = time();
        $diff = ($now-$time);
        $secondsLeft = $diff;

        for($i=6;$i>-1;$i--)
        {
            $w[$i] = intval($secondsLeft/$d[$i][0]);
            $secondsLeft -= ($w[$i]*$d[$i][0]);
            if($w[$i]!=0)
            {
                $return.= abs($w[$i]) . " " . $d[$i][1] . (($w[$i]>1)?'s':'') ." ";
            }

        }

        $return .= ($diff>0)?"ago":"left";
        return $return;
    }

    function FirstRelativeTime($time) {
        $time = strtotime($time);
        $d[0] = array(1,"second");
        $d[1] = array(60,"minute");
        $d[2] = array(3600,"hour");
        $d[3] = array(86400,"day");
        $d[4] = array(604800,"week");
        $d[5] = array(2592000,"month");
        $d[6] = array(31104000,"year");

        $w = array();

        $return = "";
        $now = time();
        $diff = ($now-$time);
        $secondsLeft = $diff;

        for($i=6;$i>-1;$i--)
        {
            $w[$i] = intval($secondsLeft/$d[$i][0]);
            $secondsLeft -= ($w[$i]*$d[$i][0]);
            if($w[$i]!=0)
            {
                $return.= abs($w[$i]) . " " . $d[$i][1] . (($w[$i]>1)?'s':'') ." ";
                break;
            }

        }

        $return .= ($diff>0)?"ago":"left";
        return $return;
    }
/*
    function resizeImage($file, $fileNameToStore) {
        // Resize image
        $resize = Image::make($file)->resize(600, null, function ($constraint) {
        $constraint->aspectRatio();
        })->encode('jpg');

        // Create hash value
        $hash = md5($resize->__toString());

        // Prepare qualified image name
        $image = $hash."jpg";

        // Put image to storage
        $save = Storage::put("public/images/{$fileNameToStore}", $resize->__toString());

        if($save) {
        return true;
        }
        return false;
    }
*/
  
?>