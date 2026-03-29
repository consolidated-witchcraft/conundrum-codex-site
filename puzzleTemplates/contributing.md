# Contributing
To contribute a puzzle to the Conundrum Codex, you MUST follow this folder structure to enable auto-discovery:


puzzlename
- /template.json
- /images/
- /sass/
- /typescript/puzzlenameBoot.ts

## Building
- Any .scss files in the sass subfolder will be auto-compiled, and you will find it them /contrib/puzzlename/css/
- Images will be automatically converted to webp
- Only puzzlenameBoot.ts will be compiled - other TypeScript files will be cooked into the final output js.

## template.json Field Reference
template.json is the beating heart of the puzzles system; it's what allows users to build instances of your puzzles without your ever having to write a line of PHP.

However, it's also extremely strict! Certain fields are required, and builds without them will fail.

| Field name    | Field Description                                                                                                                                                                          | Accepted values                                                                     | Required? |
|---------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------|-----------|
| title         | The human-readable name of your puzzle template                                                                                                                                            | Alphanumeric characters, spaces, hyphens and underscros                             | YES       |
| slug          | The machine-readable name of your puzzle template. This MUST be unique in the system                                                                                                       | Alphanumeric characters, hyphens and underscores ONLY                               | YES       |
| hidden        | Whether or not this template is hidden. Hidden templates will still be built, but will not appear on the Puzzle Templates index.                                                           |                                                                                     |           |
| type          | The type of your template. Either 'puzzle' or 'tool'                                                                                                                                       | 'puzzle' or 'tool'                                                                  | YES       |
| credits       | An array of objects containing the credits that should appear on the template's page.                                                                                                      | [{"name": "Your Name", "email": "Your E-Mail address", "created": "Code, Styling"}] | NO        |
| created       | When this template was created.                                                                                                                                                            | A date string, formatted as 'Y-m-d'                                                 | YES       |
| configOptions | What options the user should be able to configure when creating an instance of your template. An array of configOption objects. See 'configOption field reference' below for more details. | See configOptions field reference                                                   | YES       |

## configOptions Field Reference
Whatever you define in this array will be presented to the user when configuring an instance of your template.
You'll be able to see the serialized versions of these configurations for each instance when your template's code retrieves them from the API.

Because these options control systems that will accept raw user input and store them in the database, they are incredibly strict. 
Each config option MUST be of a type known to the system. Do NOT attempt to create your own; your template will be rejected when you attempt to rebuild the template cache.

The structure of configOptions is deceptively simple. It's an array of JSON objects with these keys:
```json
[{
    "configName": "clue",
    "label": "Clue",
    "type": "string",
    "helpText": " A clue that will be presented to the player."
}]
```
### Config Option Key Reference
*All fields in a config option are mandatory!*

| Key        | Description                                                                                                                | Values                                                            | Notes                                                                                                                                                                                                                                                                                                            |
|------------|----------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| configName | The machine name of this config option. MUST be unique within this template.                                               | Alphanumeric characters and underscores                           | Internally, this is what your field will be called in the html.                                                                                                                                                                                                                                                  |
| label      | The human-readable label for your config option                                                                            | Alphanumeric characters, spaces, hyphens and full-stops (periods) |                                                                                                                                                                                                                                                                                                                  |
| type       | The type of widget that is presented to your user to fill in when configuring this option.                                 | One of the 'Known Config Option Types' below                      | The system ONLY knows how to configure the types below. All others will be rejected. The Type doesn't just control the form widget presented to the user; it also controls the field's validation, serialization and deserialization. Again, any attempt to use a type the system doesn't know about won't work. |
| helpText   | The help text that will be displayed beneath the field. This gives a hint to the user on how to use your field effectively | Alphanumeric characters, spaces, hyphens and full-stops (periods) | Keep it brief!                                                                                                                                                                                                                                                                                                   |

#### Known Config Options Types
| Type name    | Accepted Values                                                                                                                       | HTML Field Produced                                                                                                                                                                                                                                                |
|--------------|---------------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| string       | A single string                                                                                                                       | ```<input type="text">```                                                                                                                                                                                                                                          |
| integer      | A single positive integer                                                                                                             | ```<input type="number" min="0" max="100">```                                                                                                                                                                                                                      |
| stringArray  | An array of string fields to which the user may add as many entries as they like                                                      | ```<input name="yourField[1]" type="text"><br/><input name="yourField[2]" type="text">```                                                                                                                                                                          |
| integerArray | An array of integer fields to which the user may add as many entries as they like                                                     | ```<input name="yourField[1]" type="number" min="0" max="100"><br/><input name="yourField[2]" type="number" min="0" max="100">```                                                                                                                                  |
| dieRoll      | A string recognised by the dieRoll API. So, 3d20p5 entered here will result in a die roll of three d20s, plus five added to the total | A specialised die-roll widget                                                                                                                                                                                                                                      |
| route        | An internal field which requires matching a route. Not usable in this context.                                                        | N/A                                                                                                                                                                                                                                                                |
| boolean      | A simple boolean field                                                                                                                | ```<input type="radio" id="yourFieldName[1]" name="yourFieldName" value="true"><label for="yourFieldName[1]>True</label></input><input type="radio" id="yourFieldName[1]" name="yourFieldName" value="false"><label for="yourFieldName[2]>False</label></input>``` |
| booleanArray | An array of boolean fields                                                                                                            | Specialised Widget                                                                                                                                                                                                                                                 |
| keyValueList | An array of key-value pairs                                                                                                           | Specialised Widget                                                                                                                                                                                                                                                 |

## Writing your JavaScript
Whatever TypeScript you define in puzzlenameBoot.ts will be automatically loaded a user lands on an instance of your puzzle.
The HTML presented when your user lands on an instance is very simple; a div with the id ```puzzle-container```. 

It has two data properties
- ```data-templateslug``` <- Your template's slug
- ```data-instancecode``` <- The unique instance code of the puzzle instance being played
- ```data-userid``` <- The user ID of the player.

Using this div, and retrieving these two data means you have all the information you need to interact with the API.
Make a GET request (with an authorised user) /api/puzzles/instances/dynamic/{templateSlug}/{instanceCode}
- This will return the serialized configuration for this instance.
Build your puzzle as you see fit.

If you want to inform the system of your players' progress, you can make a post request to:
/api/user/{userId}/playEvents with the JSON body:
{
    "type": "instance_start",
    "puzzleInstanceCode": "string",
    "puzzleTemplateSlug": "string"
}
