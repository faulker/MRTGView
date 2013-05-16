MRTG View
========

A better way to view MRTG graphs.


Install
-------

1. Download MRTG View and extract it to your www root folder.
2. Edit the 'config.php' and change $mrtg_path to reflect where MRTG is storing its graphs.
3. You're done; point your browser at the MRTG View path.


There is still a lot that needs to be done but it is a good start.


MRTG Notes
------
MRTG has to be run with the _'--subdirs=HOSTNAME_SNMPNAME'_ configuration for MRTG View to work.

I use the fallowing cfgmaker switches:

```
cfgmaker\
        --no-down\
        --ifref=nr\
        --ifdesc=descr\
        --global "WorkDir: /var/www/mrtg"\
        --global "options[_]: bits"\
        --subdirs=HOSTNAME_SNMPNAME
```