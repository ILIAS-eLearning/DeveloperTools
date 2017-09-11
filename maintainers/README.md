# How to use this script
First of all use composer to install all dependencies:
```
$ sudo apt-get install php-7.0 php7.0-mbstring
$ composer install 
```

Use the commandline php to run the script:
````
Usage: run.php [-c cmd, --cmd cmd] [-p path, --path path (default: /var/www/ilias)]

Optional Arguments:
	-p path, --path path (default: /var/www/ilias)
		base Path of the ILIAS-Installation
````
You need PHP7 to run this script.

The following commands are available:
```
php run.php -c maintainers # Lists all already registered maintainers
php run.php -c components # Lists alls already registered components
php run.php -c generate # generated the /docs/documentation/maintenance.md File
php run.php -c usage # Lists all available commands
```

### What does it?
This reads/generates a maintenance.json in every subdirectory of /Services, /Modules and /src of the ILIAS. 
Those files are structures like this:
```
{
    "maintenance_model": "Classic", // Classic or Service 
    "first_maintainer": "fschmid(21087)", // Username of ILIAS.de with usr_id in brackets
    "second_maintainer": "", // Same format as first_maintainer
    "implicit_maintainers": [
        "mstuder(8473)" // Same format as first_maintainer
    ],
    "coordinator": "", // Same format as first_maintainer
    "tester": "", // Same format as first_maintainer
    "testcase_writer": "", // Same format as first_maintainer
    "path": "Modules/Bibliographic", // Will be generated automatically
    "belong_to_component": "Bibliographic List Item"
    "used_in_components": [
        "FileDelivery" // List every Component which uses ode from this directory. If nearly the whole codebase uses it, write "All". If nobody uses it write "None"
    ]
}
```
After running the files in this repo
- maintainers.json  
- components.json  
will be updated.  

### Configuration
- The components.json has to be filled out manually with the information which Maintainer is first and so on.
- You can update the maintainers.json with changed usr_ids. 

### <a name="howto"></a>I'm a Maintainer, what shall I do now?
We accepted to use this script to identify maintained and unmaintained code in ILIAS (see PR [#471](https://github.com/ILIAS-eLearning/ILIAS/pull/471#issuecomment-292930088)).

**Step 1:**
Checkout the DeveloperTool-Repository within your local ILIAS-Repo:
```
$ cd Customizing/global
$ git clone https://github.com/ILIAS-eLearning/DeveloperTools tools
```

**Step 2:**
Install the dependencies:
```
$ cd tools/maintainers
$ composer install
$ sudo apt-get install php7.0-mbstring
```

**Step 3:**
Check if your maintainer-account is already registered:
```
$ php run.php -c maintainers
Available Maintainers:


---------------------------------
| username              | model |
=================================
| fschmid               | 21087 |
---------------------------------
| kunkel                | 115   |

...

```

If not, check the file maintainers.json. Is there already an entry with your ILIAS.de-Account and User-ID? 
Add a line
```
	"username": "username(123)",
```
if missing.

**Step 4:**
Check if your component already are registered:
```
$ php run.php -c components

Available Components:

-------------------------------------------
| name                          | model   |
===========================================
| Administration                | Classic |
-------------------------------------------
| Authentication & Registration | Classic |
-------------------------------------------
| Bookmarks                     | Classic |
-------------------------------------------
...
```

If not, check the components.json. Are all your components listed there? Add one, e.g.
```
"RBAC": {
        "directories": [],
        "name": "RBAC",
        "first_maintainer": "smeyer",
        "second_maintainer": "smeyer",
        "tester": "username(123)",
        "testcase_writer": "username(123)",
        "modell": "Classic",
        "coordinators": []
    },
```
if your component is missing. You just have to fill out "Component Name", "name" and 
"first_maintainer". The "modell" is "Classic" for nearly everything. "directories" will be 
filled out automatically.

**Step 5:**
Run the script (see above). If there is a maintenance.json missing in your components directories, 
it will be genereated now.
After that, open and edit the desired maintenance.json-File in your directory, e.g.:

```
vi /Services/AccessControl/maintenance.json

{
    "maintenance_model": "Classic", # Use Classic here
    "first_maintainer": "", # Your account-username (e.g. fschmid works as well as fschmid(21087))
    "second_maintainer": "", # Same format as first-maintainer
    "implicit_maintainers": [], # Same format as first-maintainer
    "coordinator": "", # Same format as first-maintainer
    "tester": "", # Same format as first-maintainer
    "testcase_writer": "", # Same format as first-maintainer
    "path": "Services/AccessControl", # You do not have to change this
    "belong_to_component": "None", # Write teh exact name of the component as its registered in components.json
    "used_in_components": [] # If this directory is used in several components, list them (with the exact name).
}
```
**Step 6:**
Run the file-generation
```
$ php run.php -c generate
ILIAS has 29 maintained and 148 unmaintained Directories in 43 components
Writing MD-File

```
The files 
- maintainers.json
- components.json
- maintenance.md (in the ILIAS-repository)

will be updated. Push the changes in this repository. The chnages in the ILIAS repository currently 
can be pushed, too. After we completed the file, changes are only allowed by Pull Request.
 
If you have any question feel free to conatact fs@studer-raimann.ch
