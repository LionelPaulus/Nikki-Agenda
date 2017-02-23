<?php
namespace AppBundle\Service;

class SuperpositionKillerService
{

    public function superpositionKiller($array)
    {
        for ($i=0; $i < count($array); $i++) {
            array_splice($array, 0, 0);
            // Not the last
            if ($i < count($array) - 1) {
                // If next busy start before current busy end
                if ($array[$i+1]["start"] < $array[$i]["end"]) {
                    // If next busy end before or at the same time than the current busy
                    if ($array[$i+1]["end"] <= $array[$i]["end"]) {
                        // Delete next busy
                        unset($array[$i+1]);
                        // echo "Unset A ".($i+1)."<br>";
                        $i--;
                        continue;
                    }

                    // If next busy end after current busy end
                    if ($array[$i+1]["end"] > $array[$i]["end"]) {
                        // Replace current busy end by next busy end and delete next busy
                        $array[$i]["end"] = $array[$i+1]["end"];
                        unset($array[$i+1]);
                        // echo "Unset E ".($i+1)."<br>";
                        $i--;
                        continue;
                    }
                }

                // If next busy start at the same time than current busy
                if ($array[$i+1]["start"] === $array[$i]["start"]) {
                    // If next busy end before or at the same time than the current busy
                    if ($array[$i+1]["end"] <= $array[$i]["end"]) {
                        // Delete next busy
                        unset($array[$i+1]);
                        // echo "Unset C ".($i+1)."<br>";
                        $i--;
                        continue;
                    }

                    // If next busy end after thant current busy
                    if ($array[$i+1]["end"] > $array[$i]["end"]) {
                        // Replace current busy end by next busy end and delete next busy
                        $array[$i]["end"] = $array[$i+1]["end"];
                        unset($array[$i+1]);
                        // echo "Unset D ".($i+1)."<br>";
                        $i--;
                        continue;
                    }
                }

                // If next busy start at the same time than current busy end
                if ($array[$i+1]["start"] === $array[$i]["end"]) {
                    // Replace current busy end by next busy end and delete next busy
                    $array[$i]["end"] = $array[$i+1]["end"];
                    unset($array[$i+1]);
                    // echo "Unset B ".($i+1)."<br>";
                    $i--;
                    continue;
                }
            }
            // echo "<hr>";
        }

        return $array;
    }
}
