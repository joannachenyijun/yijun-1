#!/bin/bash
        for i in `seq -w 01 16`;
        do
                python ./generateHeatMap.py ../filteredCrimesByYear20$i.csv crimes20$i.bmp
        done    