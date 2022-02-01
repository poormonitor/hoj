#!/bin/bash
cd judged
make
make install
make clean
cd ../judge_client
make
make install
make clean
cd ../sim/sim_3_0_1
make fresh
make exes
chmod +x sim*
cp sim_c.exe /usr/bin/sim_c
cp sim_c++.exe /usr/bin/sim_cc
cp sim_java.exe /usr/bin/sim_java
cp sim_pasc.exe /usr/bin/sim_pas
cp sim_text.exe /usr/bin/sim_text
cp sim_lisp.exe /usr/bin/sim_scm
cp sim_py.exe /usr/bin/sim_py
make fresh
cd ..
cp sim.sh /usr/bin
chmod +x /usr/bin/sim.sh
#ln -fs /usr/bin/sim_c /usr/bin/sim_cc 2>&1 > /dev/null
echo "Core part done!"
