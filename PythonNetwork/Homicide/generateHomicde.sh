#!/bin/bash
		python ./generateHomicideCluster.py ./HomicideCluster1.csv "../Chicago 41.8315,-87.7265.jpeg" 1 hom1.bmp
        for i in `seq 2 10`;
        do
                python ./generateHomicideCluster.py ./HomicideCluster$i.csv hom$((i-1)).bmp $i hom$i.bmp
        done    