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

Will create a new story, requires, at the very least, a string value for the name parameter. Currently accepts parameters in form-data. Token with key 'token' required in header.

- Header > token: string (required)
- name: string (required)
- description: string (optional)
- priority: string (optional)
- dependency: int (optional) - this if a foreign key relating to the id of the parent story
- time-size: int
- epic-id: int - this is a foreign key relating to the id of the parent epic

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
