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
            // echo $i."<br>";
            // Not the last
            if ($i < count($array) - 1) {
                // If next busy start before current busy end
                if ($array[$i+1]["start"] < $array[$i]["end"]) {
                    // If next busy end before or at the same time than the current busy
                    if ($array[$i+1]["end"] <= $array[$i]["end"]) {
                        // Delete next busy
                        unset($array[$i]);
                        array_values($array);
                        break;
                        $i--;
                        // echo "Unset A ".($i+1)."<br>";
                    }

                    // If next busy end after current busy end
                    if ($array[$i+1]["end"] > $array[$i]["end"]) {
                        // Replace current busy end by next busy end and delete next busy
                        $array[$i]["end"] = $array[$i+1]["end"];
                        unset($array[$i]);
                        array_values($array);
                        break;
                        $i--;
                        // echo "Unset E ".($i+1)."<br>";
                    }
                }

                // If next busy start at the same time than current busy
                if ($array[$i+1]["start"] === $array[$i]["start"]) {
                    // If next busy end before or at the same time than the current busy
                    if ($array[$i+1]["end"] <= $array[$i]["end"]) {
                        // Delete next busy
                        unset($array[$i]);
                        array_values($array);
                        break;
                        $i--;
                        // echo "Unset C ".($i+1)."<br>";
                    }

                    // If next busy end after thant current busy
                    if ($array[$i+1]["end"] > $array[$i]["end"]) {
                        // Replace current busy end by next busy end and delete next busy
                        $array[$i]["end"] = $array[$i+1]["end"];
                        unset($array[$i]);
                        array_values($array);
                        break;
                        $i--;
                        // echo "Unset D ".($i+1)."<br>";
                    }
                }

                // If next busy start at the same time than current busy end
                if ($array[$i+1]["start"] === $array[$i]["end"]) {
                    // Replace current busy end by next busy end and delete next busy
                    $array[$i]["end"] = $array[$i+1]["end"];
                    unset($array[$i]);
                    array_values($array);
                    break;
                    $i--;
                    // echo "Unset B ".($i+1)."<br>";
                }
            }
            // echo "<hr>";
        }

        // dump(array_values($array));
        // die();

        return array_values($array);
    }
}
