<?php

function primMST($graph){

    $vertices = count($graph);
    $selected = array_fill(0,$vertices,false);
    $selected[0] = true;

    $edges = 0;

    echo "<h3>Optimized Delivery Routes</h3>";
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>From</th><th>To</th><th>Distance</th></tr>";

    while($edges < $vertices-1){

        $min = INF;
        $x=0; $y=0;

        for($i=0;$i<$vertices;$i++){
            if($selected[$i]){
                for($j=0;$j<$vertices;$j++){
                    if(!$selected[$j] && $graph[$i][$j]){
                        if($min > $graph[$i][$j]){
                            $min = $graph[$i][$j];
                            $x=$i; $y=$j;
                        }
                    }
                }
            }
        }

        echo "<tr>
                <td>$x</td>
                <td>$y</td>
                <td>".$graph[$x][$y]."</td>
              </tr>";

        $selected[$y]=true;
        $edges++;
    }

    echo "</table>";
}

$graph = [
 [0,10,6,5],
 [10,0,0,15],
 [6,0,0,4],
 [5,15,4,0]
];

primMST($graph);

?>
