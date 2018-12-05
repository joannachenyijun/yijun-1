#!/bin/bash
		python ./generateBatteryCluster.py ./BatteryCluster1.csv "../../Chicago 41.8315,-87.7265.jpeg" 1 bat1.bmp
        for i in `seq 2 10`;
        do
                python ./generateBatteryCluster.py ./BatteryCluster$i.csv bat$((i-1)).bmp $i bat$i.bmp
        done    