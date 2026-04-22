#!/bin/bash
cd '/r/COOLYEAHHH/.SEM 8/myzyngga'
FILTER_BRANCH_SQUELCH_WARNING=1 git filter-branch -f --env-filter '
TS=$(echo $GIT_AUTHOR_DATE | cut -d" " -f1 | tr -d "@")
if [ "$TS" -lt 1735689600 ] 2>/dev/null; then
  export GIT_AUTHOR_DATE="1771822800 +0700"
  export GIT_COMMITTER_DATE="1771822800 +0700"
fi
' -- --all
echo DONE