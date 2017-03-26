@ECHO OFF

IF EXIST "%1" (
    cd %1 
) ELSE (
    cd /d %~dp0
)

vagrant up

PAUSE

CLS

EXIT