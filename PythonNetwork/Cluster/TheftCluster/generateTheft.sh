#!/bin/bash
		python ./generateTheftCluster.py ./TheftCluster1.csv "../../Chicago 41.8315,-87.7265.jpeg" 1 theft1.bmp
        for i in `seq 2 10`;
        do
                python ./generateTheftCluster.py ./TheftCluster$i.csv theft$((i-1)).bmp $i theft$i.bmp
        done    