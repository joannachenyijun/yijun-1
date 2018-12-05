#!/bin/bash
		python ./generateDamageCluster.py ./CriminalDamageCluster1.csv "../../Chicago 41.8315,-87.7265.jpeg" 1 dam1.bmp
        for i in `seq 2 10`;
        do
                python ./generateDamageCluster.py ./CriminalDamageCluster$i.csv dam$((i-1)).bmp $i dam$i.bmp
        done    