**SSRTDC Springer article parser**

Parser is intended for building the list of articles from Springer journal.
Parsing rules:
- get issue page url from journal
- get article url from issue page
- get Author, Year, Journal, Volume, Issue, Pages, DOI, DOI Link, Paper Title, Complete Citation, Abstract, Views, Citations data
- find article name in  https://scholar.google.com.ua/ and get number of citations.

 
####1. Dependencies
 * php 5.6
 * guzzlehttp/guzzle ~6.0
####2. Installation
 - Clone repository or download zip
 - Run in document root
 ```bash
 composer install
 ```
 - Configure database settings in **src/Config/defaults.conf.php**
####3. Run Scripts for Project
 ```bash
 #run hournal parsing. For this a link to journal and a name of future csv where data will be stored should be passed.
 php index.php JOURNAL_LINK CSV_FILE_NAME
 #When journal is passed, command should be executed in order to parse data from  google scholar
 php single.php
 ```
### 4. License
 This package is using [MIT](LICENSE.md).