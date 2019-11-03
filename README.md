# ENDPOINTS

## STORIES

### GET requests

#### /stories

No parameters required, will respond with all the stories as the result.

#### /stories/\${NUMBER}

No parameters required, use the story id in the path to get the tasks and comments related to that story
ex: /stories/1

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
