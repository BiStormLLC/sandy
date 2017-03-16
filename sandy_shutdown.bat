@ECHO OFF

IF EXIST "%1" (
    cd %1
    vagrant halt -f
) ELSE (
    cd %~dp0
    vagrant halt -f
)

PAUSE

CLS

EXIT