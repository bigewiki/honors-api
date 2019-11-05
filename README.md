# ENDPOINTS

## STORIES

### GET requests

#### /stories

No parameters required, will respond with all the stories as the result.

#### /stories/\${NUMBER}

No parameters required, use the story id in the path to get the tasks and comments related to that story
ex: /stories/1

### POST requests

#### /stories

Will create a new story, requires, at the very least, a string value for the name parameter.

name: string (required)
description: string (optional)
priority: string (optional)
dependency: int (optional) - this if a foreign key relating to the id of the parent story
time-size: int
epic-id: int - this is a foreign key relating to the id of the parent epic

### DELETE requests

#### /stories/\${id}

No parameters required, use the story id in the path to delete that story. Will fail if story has associated comments, tasks, or child dependencies (foreign key check failure).
ex: /stories/34

## SPRINTS

### GET requests

#### /sprints

No parameters required, will respond with all the sprints as the result.

#### /sprints/current

No parameters required, will respond with the stories from the current sprint

#### /sprints/last

No parameters required, will respond with the stories from the previous sprint

#### /sprints/next

No parameters required, will respond with the stories from the next sprint

#### /sprints/future

No parameters required, will respond with the stories from 2 sprints in the future
