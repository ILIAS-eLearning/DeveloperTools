## 1-. Mark as deprecated all the variables that has a corresponding entry in the deprecated CSV file
file -> mark_deprecated_lang_vars.py

Before executing the script, is highly recommended to duplicate the language file that is wanted to modify.

This script takes a language file and adds a comment "###deprecated" at the end of each line that has a corresponding entry in
the "lang_deprecated_leg.csv" file.

This script needs two arguments:

- Ilias language file full path

- Full path of the file which contains all the deprecated values.

Example:

    python mark_deprecated_lang_vars.py /Users/Sites/ilias/lang/ilias.en.lang /Users/Doe/Desktop/lang_deprecated_leg.csv




## 2-. PARSE ENGLISH VERSION AND UPDATE ALL THE OTHER LANGUAGE FILES
file -> mark_deprecated_vars_in_other_languages.py

Before executing the script, is highly recommended to duplicate the language files that is wanted to modify.

This script parses ilias_en.lang and for each entry that contains "###deprecated" at the end,
a corresponding comment must be added in all the rest language files.

For each line which contain language string:

If the line does not contain a "###deprecated" comment, but the entry appears in the english file as deprecated, the comment ###deprecated is added at the end of the line.
All other comments existing like "###07 02 2017 new variable" are removed

If the line does contain a "###deprecated" comment, but does not appear in english version, it is removed.

Example:

    python mark_deprecated_vars_in_other_languages.py /Users/Doe/Sites/ilias5_2/ILIAS/lang/ilias_en.lang
    
    
## 3-. REMOVE ALL LINES CONTAINING ###deprecated
file -> remove_marked_deprecates.py

Before executing the script, is highly recommended to duplicate the language file that is wanted to modify.

This script parses a language file and removes lines containing "###deprecated" at the end.

Example:

    python remove_marked_deprecates.py /Users/Doe/Sites/trunk/ILIAS/lang/ilias_en.lang        