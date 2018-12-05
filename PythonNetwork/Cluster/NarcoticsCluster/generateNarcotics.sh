#!/bin/bash
		python ./generateNarcoticsCluster.py ./NarcoticsCluster1.csv "../../Chicago 41.8315,-87.7265.jpeg" 1 narc1.bmp
        for i in `seq 2 10`;
        do
                python ./generateNarcoticsCluster.py ./NarcoticsCluster$i.csv narc$((i-1)).bmp $i narc$i.bmp
        done    