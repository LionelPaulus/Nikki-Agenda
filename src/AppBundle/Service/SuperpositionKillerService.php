<?php
namespace AppBundle\Service;

class SuperpositionKillerService
{

    public function superpositionKiller($array)
    {
        $new_array = [];

        // Copy the array
        for ($i=0; $i < count($array); $i++) {
            $new_array[$i] = $array[$i];
        }

        for ($i=0; $i < count($array); $i++) {
            $unset = false;
            // echo $i."<br>";
            // Not the last
            if ($i < count($array) - 1) {
                // If next busy start before current busy end
                if ($array[$i+1]["start"] < $array[$i]["end"]) {
                    // If next busy end before or at the same time than the current busy
                    if ($array[$i+1]["end"] <= $array[$i]["end"]) {
                        // Delete next busy
                        unset($new_array[$i+1]);
                        $unset = true;
                        $i++;
                        // echo "Unset A ".($i+1)."<br>";
                    }

                    // If next busy end after current busy end
                    if (($array[$i+1]["end"] > $array[$i]["end"])&&($unset == false)) {
                        // Replace current busy end by next busy end and delete next busy
                        $new_array[$i]["end"] = $new_array[$i+1]["end"];
                        unset($new_array[$i+1]);
                        $i++;
                        // echo "Unset E ".($i+1)."<br>";
                    }
                }

                // If next busy start at the same time than current busy
                if (($array[$i+1]["start"] === $array[$i]["start"])&&($unset == false)) {
                    // If next busy end before or at the same time than the current busy
                    if ($array[$i+1]["end"] <= $array[$i]["end"]) {
                        // Delete next busy
                        unset($new_array[$i+1]);
                        $i++;
                        // echo "Unset C ".($i+1)."<br>";
                    }

                    // If next busy end after thant current busy
                    if (($array[$i+1]["end"] > $array[$i]["end"])&&($unset == false)) {
                        // Replace current busy end by next busy end and delete next busy
                        $new_array[$i]["end"] = $new_array[$i+1]["end"];
                        unset($new_array[$i+1]);
                        $i++;
                        // echo "Unset D ".($i+1)."<br>";
                    }
                }

                // If next busy start at the same time than current busy end
                if (($array[$i+1]["start"] === $array[$i]["end"])&&($unset == false)) {
                    // Replace current busy end by next busy end and delete next busy
                    $new_array[$i]["end"] = $new_array[$i+1]["end"];
                    unset($new_array[$i+1]);
                    $i++;
                    // echo "Unset B ".($i+1)."<br>";
                }
            }
            // echo "<hr>";
        }

        return array_values($new_array);
    }
}
