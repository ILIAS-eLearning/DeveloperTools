# Disclaimer

Only use this in unixy environment at your own risk.


# Githooks-Developer Tools

Introducing Githooks in order to streamline the
development and catch possible errors at an early stage
of development.


# Introduction

Githooks are scripts that are called at certain events
in a git-repository, e.g. pre-commit or post-commit, which
are called (nomen est omen) right  before or after a commit
process is initiatet (for a complete list of possible githooks
please refer to git documentation).

They are placed in the `.git/hooks/`, but are called in the
repository root. The script `install`, that should be called from
the repo root (`./git_hooks/install`), will do this for you atomatically.
Note, you need composer to be installed and defined as global var.
You may add your hooks simply by adding them into the git_hooks/hooks folder.
We take this approach, since there is no simple way to push local
hooks to a remote repo.
Utility-scripts, which are called by the git hooks, should be placed
in the `support`-folder. Also, please refer to the already present
pre-commit hook, which should serve as a fine example.

# Options

## Skip linting at next commit

When using staged commits it is not recommendet to use linting, since the
whole file will be commited. To bypass this problem one may use the skiplint
script, which will set a flag, that causes the linting step in the pre commit
to be omited at next commit. To set this flag, just run the skiplint script 
which is located in the git_hooks directory. 

Notice: git_hooks doesn't need to be you working directory to run skipling.
For instance, running skipling via
../git_hooks/skiplint
from the Services drectory works just fine.

You may enable linting by running skiplint again. Also, the flag will be unset,
after the next commit.

## Lint a file

You may force the linting of a file without performing any other chages by
running the lint_file script, which is located inside the git_hooks directory.
The only parameter is the path of the file of interest relative to current
directory.
