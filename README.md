# ENDPOINTS

## STORIES

### GET requests

#### /stories

No parameters required, will respond with all the stories as the result.

##### Response

- success: int
- res: array
- res[i].id: int
- res[i].name: string
- res[i].description: string
- res[i].owner: int
- res[i].sprint_id: int
- res[i].priority: string
- res[i].dependency: int
- res[i].time_size: int
- res[i].epic_id: int
- res[i].status: string

#### /stories/\${NUMBER}

No parameters required, use the story id in the path to get the tasks and comments related to that story
ex: /stories/1

##### Response

- success: int
- res: object
- res.story_id: int
- res.story_name: string
- res.story_description: string
- res.story_owner: int
- res.sprint_id: int
- res.sprint_start: date
- res.sprint_end: date
- res.upstream_dependency: int
- res.story_size: int
- res.story_epic: int
- res.story_status: string
- res.tasks: array
- res.tasks[i]: object
- res.tasks[i].task_id: int
- res.tasks[i].task_name: string
- res.tasks[i].story_name: string
- res.tasks[i].owner_id: int
- res.tasks[i].owner_name: string
- res.comments: array
- res.comments[i]: object
- res.comments[i].comment_id: int
- res.comments[i].parent_id: int
- res.comments[i].content: string
- res.comments[i].user: string

### POST requests

#### /stories

Will create a new story, requires, at the very least, a string value for the name parameter. Expects request body to be in JSON format. Token with key 'token' required in header. Response will output the new object.

##### Request

- Header > token: string (required)
- name: string (required)
- description: string (optional)
- priority: string (optional)
- dependency: int (optional) - this if a foreign key relating to the id of the parent story
- time-size: int
- epic-id: int - this is a foreign key relating to the id of the parent epic

##### Response

- success: int
- notice: string
- res: object
- res.id: int
- res.name: string
- res.description: string
- res.owner: int
- res.sprint_id: int
- res.priority: string
- res.dependency: int
- res.time_size: int
- res.epic_id: int
- res.status: string

### DELETE requests

#### /stories/\${id}

Use the story id in the path to delete that story. Will fail if story has associated comments, tasks, or child dependencies (foreign key check failure). Also requires token to be sent in header (key of 'token').
ex: /stories/34

- Header > token: string (required)

### PATCH requests

#### /stories/\${id}

Use the story id in the path to update that story. Will fail if the story does not exist. Requires key 'token' with token value in header. Accepts JSON object in request body identifying keys to be udpated:

ex path: /stories/34
ex body: {"name":"my homework","status":"in progress"}

- Header > token: string (required)
- name: string (optional)
- description: string (optional)
- owner: int (optional)
- sprint: int (optional)
- priority: string (optional)
- dependency: int (optional)
- size: int (optional)
- epic: int (optional)
- status: string (optional)

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

## USERS

### POST requests

#### /users/login

Will respond with a new API token to use in non-GET operations, a creation value, which shows when the key was created, and an expiration value, which details how long the key is valid. Requires the username (email) and password.

##### REQUEST (POST PARAMETERS):

- username: string (required)
- password: string (required)

##### RESPONSE OBJECT:

- success: int
- res: object
- res.creation: datetime
- res.expiration: datetime
- res.token: string

#### /users/check-token

Will respond with either an error or success message depending on the token sent in the request header. Expects a key of 'token' with the value of the token.

##### Request

- Header > token: string (required)

##### Response

- success: int
- res: object
- res.valid: bool
- res.message: string
