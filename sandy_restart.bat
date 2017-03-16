@ECHO OFF

IF EXIST "%1" (
    cd %1
    vagrant reload
) ELSE (
    cd %~dp0
    vagrant reload
)

PAUSE

CLS

EXIT