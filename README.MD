**SSRTDC Collections Springer PDF Downloader**

This is a script for downloading full texts of Springer journal.
Functionality:

Input data: 
  1. <list>: the name of csv file, which contains DOI and required accompanying info from articles. 
  2. <OutFolder>: the name of directory, which will store articles from the list. 
Expected output:
  1. Downloaded articles in csv format.
 
####1. Dependencies
 * php 5.6
 
####2. Installation
 - Clone repository or download zip
 - Run in document root
 ```bash
 composer install
 ```
 
####3. Run Scripts for Project
 ```bash
 #run script and pass two required parameters 
 php downloadScript.php list.csv OutFolder
 ```
 
### 4. License
 This package is using [MIT](LICENSE.md).
