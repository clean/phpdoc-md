# Changelog

## 0.18.0
- Use composer update in travis build process to cover php5.6 requirements

## 0.17.0
- Update PHP dependencies

## 0.16.0
- fix PHP Notice during view render method, when not all class poperties are set

## 0.15.0
- code refacture required to work with php7 and new DocBlock Api

## 0.14.0

- update project dependecies

## 0.13.0

- recognise native php methods in class parser and include them in class documentation

## 0.12.0

- Add "void" if no return values available
- fix for formatting of multiple @throws tags

## 0.11.0

- feature: add links to methods for bitbucket server markdown

## 0.10.0

- bugfix: correct ivalid bitbucket syntax for method name that begins with `_` char

## 0.9.0

- change the look of class markdown: add information about interfaces and extended class, inherited method moved to separate section
- remove sortMethods config parameter from .phpdoc-md
- bugfix: fatal error when not supported markdown format given

## 0.8.0

- extend: Stash markdown basic support

## 0.7.0

- feature: sortMethods config param added
