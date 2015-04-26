define({ "api": [
  {
    "description": "<p>Example route representing a fictional function of this thing. This url may mostly be arbitary, but it must match one of the URLs provided in the /functions response. SWOTY posts to this route if the user wants to active this function. At least the write token should be present.</p> <p>The response of this route is not important to SWOTY. If this things wants to notify SWOTY about changes like  starting to do something or finished doing something, it may post these information in form of status updates to SWOTY (see SWOTY REST API reference).</p> ",
    "type": "post",
    "url": "/functions/activateexamplefunction",
    "title": "Activate a specific function",
    "name": "activate_a_function",
    "group": "API",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl --header \"accesstoken: 12345\" -i http://localhost:3000/functions/activateexamplefunction",
        "type": "curl"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "accesstoken",
            "description": "<p>Access token to activate this function. Should at least be the write token.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"accesstoken\": \"12345\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": true,
            "field": "message",
            "description": "<p>Success message.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"message\" : \"Function activated.\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "./app.js",
    "groupTitle": "API"
  },
  {
    "type": "get",
    "url": "/deregister",
    "title": "Deregister this thing",
    "name": "deregister",
    "group": "API",
    "description": "<p>Deregister Action. SWOTY calls this route, when a user wants to remove this thing from his profile. This may be in case he/she doesn&#39;t want this thing anymore or he/she wants to give it to another user. The internal access token/register token should be marked as unused so that device to ready to be registered again.</p> <p>The owner token should be passed to activate this function. The token is passed by request header &quot;accesstoken&quot;.</p> ",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl --header \"accesstoken: 12345\" -i http://localhost:3000/deregister",
        "type": "curl"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "accesstoken",
            "description": "<p>Access token to activate this function. Should be the owner token.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"accesstoken\": \"12345\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": true,
            "field": "message",
            "description": "<p>Success message.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"message\" : \"Device deregistered.\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "./app.js",
    "groupTitle": "API"
  },
  {
    "type": "get",
    "url": "/functions",
    "title": "List of available functions",
    "name": "functions",
    "group": "API",
    "description": "<p>This route should give a list of the available functions which can be activated by a REST call. Every function contains:</p> <ul> <li>a unique name for identification</li> <li>an URL which is called to activate the function</li> <li>an indicator if this function is available</li> <li>a list of parameters</li> </ul> <p>Every parameters has</p> <ul> <li>a unique name</li> <li>a type which will determine the representation on SWOTY</li> <li>an indicator if the parameter is optional / required</li> <li>a list of constraints</li> </ul> <p>Every constraint has:</p> <ul> <li>a type</li> <li>an error message if the user input (on SWOTY) does not match the constraint. This means: every input of the user will be validated server side by SWOTY before submitting the data to the network!</li> </ul> <p>If the functions of this thing change anytime after registrations, SWOTY should be notified about that. (See Example call to Swoty to notify a change in the function list)</p> ",
    "examples": [
      {
        "title": "Example call to Swoty to notify a change in the function list",
        "content": "curl -i --header \"accesstoken: mynetworkaccesstoken\" http://urltoswoty.com/api/v1/information/update",
        "type": "curl"
      },
      {
        "title": "Call by SWOTY to retrieve function list",
        "content": "curl -i --header \"accesstoken: 12345\" http://localhost:3000/functions",
        "type": "curl"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "accesstoken",
            "description": "<p>Access token to retrieve function list. Should be the read token.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"accesstoken\": \"12345\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Object[]",
            "optional": false,
            "field": "functions",
            "description": "<p>List of available functions</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": ".name",
            "description": "<p>Unique name of the function</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": ".url",
            "description": "<p>Url used by SWOTY to activate this function (POST)</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": ".available",
            "description": "<p>Indicates if this function may be called at the moment</p> "
          },
          {
            "group": "Success 200",
            "type": "Object[]",
            "optional": false,
            "field": ".parameters",
            "description": "<p>A List of parameters required to activate this function. Every parameter will be set by user input. Representation depends on the parameter type.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": ".parameters.name",
            "description": "<p>The name of the parameter. Will be used as the label text for the corresponding input. Should be unique withing the function. Other functions may vontain a parameter with the same name.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": ".parameters.type",
            "description": "<p>The type of the parameter. </p> <p>Currently supported:</p> <ul> <li>integer</li> <li>Choice</li> <li>text</li> </ul> "
          },
          {
            "group": "Success 200",
            "type": "String[]",
            "optional": true,
            "field": ".parameters.choices",
            "description": "<p>Required when using &quot;Choice&quot; as parameter type. Contains a list of predefined values / options. SWOTY will send the index of the selected choice with an activate function request.</p> "
          },
          {
            "group": "Success 200",
            "type": "Boolean",
            "optional": true,
            "field": ".parameters.multiple",
            "defaultValue": "false",
            "description": "<p>Only evaluated when using &quot;Choice&quot; as parameter type. Lets the user select multiple values from the choice list.</p> "
          },
          {
            "group": "Success 200",
            "type": "Boolean",
            "optional": true,
            "field": ".parameters.expanded",
            "defaultValue": "false",
            "description": "<p>Only evaluated when using &quot;Choice&quot; as parameter type. </p> <ul> <li>false: representation will be a dropdown list</li> <li>true: representation will be a radio select list Note: the represenattion will vary if multiple is set to true</li> </ul> "
          },
          {
            "group": "Success 200",
            "type": "Boolean",
            "optional": false,
            "field": ".parameters.required",
            "description": "<p>Indicates if the user must fill in this value.</p> "
          },
          {
            "group": "Success 200",
            "type": "Boolean",
            "optional": true,
            "field": ".parameters.readOnly",
            "defaultValue": "false",
            "description": "<p>Indicates if the parameter value is editable. If true, only the default value will be displayed if provided.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": true,
            "field": ".parameters.defaultValue",
            "description": "<p>The default value for this parameter.</p> "
          },
          {
            "group": "Success 200",
            "type": "Object[]",
            "optional": true,
            "field": ".parameters.constraints",
            "description": "<p>A list of contraints for this parameter. SWOTY will validate the form input server side by this list before sending it to this thing.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": ".parameters.constraints.type",
            "description": "<p>The type of this constraint.  Currently supperted:</p> <ul> <li>NotNull</li> <li>NotBlank</li> <li>Date</li> <li>DateTime</li> <li>Choice</li> <li>Country</li> <li>GreatedThan</li> <li>LessThan</li> <li>Language</li> <li>Locale</li> <li>Time</li> </ul> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": ".parameters.constraints.message",
            "description": "<p>This message will be shown to the user if his input does not validate.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Example Success-Response (tries to list all available configuration options):",
          "content": "    HTTP/1.1 200 OK\n    {\n    \t\"functions\": [{\n\t        \"name\": \"Function-A\",\n\t        \"url\": \"http://www.example.com\",\n\t        \"available\": true,\n\t        \"parameters\": [\n\t            {\n\t                \"name\": \"Param-A\",\n\t                \"type\": \"integer\",\n\t                \"required\": true,\n\t                \"constraints\": [\n\t                    {\n\t                        \"type\": \"NotNull\",\n\t                        \"message\": \"Param A may not be null\"\n\t                    }\n\t                ]\n\t            },\n\t            {\n\t                \"name\": \"Param-B\",\n\t                \"type\": \"Choice\",\n\t                \"choices\": [\n\t                    1, 2, 3, 4, 5\n\t                ],\n\t                \"required\": false,\n\t                \"constraints\": [\n\t                    {\n\t                        \"type\": \"NotBlank\",\n\t                        \"message\": \"Param B may not be blank\"\n\t                    }\n\t                ]\n\t            },\n\t            {\n\t                \"name\": \"Param-C\",\n\t                \"type\": \"Choice\",\n\t                \"multiple\": true,\n\t                \"expanded\": false,\n\t                \"choices\": [\n\t                    1, 2, 3, 4, 5\n\t                ],\n\t                \"required\": false,\n\t                \"constraints\": [\n\t                    {\n\t                        \"type\": \"NotBlank\",\n\t                        \"message\": \"Param C may not be blank\"\n\t                    }\n\t                ]\n\t            },\n\t            {\n\t                \"name\": \"Param-D\",\n\t                \"type\": \"Choice\",\n\t                \"multiple\": true,\n\t                \"expanded\": true,\n\t                \"choices\": [\n\t                    1, 2, 3, 4, 5\n\t                ],\n\t                \"required\": false,\n\t                \"constraints\": [\n\t                    {\n\t                        \"type\": \"NotBlank\",\n\t                        \"message\": \"Param D may not be blank\"\n\t                    }\n\t                ]\n\t            }\n\t    ]}, {\n\t        \"name\": \"Function-B\",\n\t        \"url\": \"http://www.example.com\",\n\t        \"available\": true,\n\t        \"parameters\": [\n\t            {\n\t                \"name\": \"Param-2A\",\n\t                \"type\": \"text\",\n\t                \"required\": true,\n\t                \"constraints\": [\n\t                    {\n\t                        \"type\": \"Locale\",\n\t                        \"message\": \"Param 2A must be a locale\"\n\t                    }\n\t                ],\n\t                \"defaultValue\": \"hallo\"\n\t            },\n\t            {\n\t                \"name\": \"Param-2B\",\n\t                \"type\": \"integer\",\n\t                \"required\": false,\n\t                \"readOnly\": true,\n\t                \"defaultValue\": 20\n\t            }\n\t        ]\n\t\t}]\n\t}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "./app.js",
    "groupTitle": "API"
  },
  {
    "description": "<p>Returns a list of information about this thing. These information should represent the current status. If the status changes, SWOTY should be notified about that. To do that, just make a POST request to SWOTY, the network will then retrieve the updated information from this route, See SWOTY REST Api documentation for more details.</p> ",
    "type": "get",
    "url": "/functions/information",
    "title": "Information about this thing and it's status.",
    "name": "information",
    "group": "API",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl --header \"accesstoken: 12345\" -i http://localhost:3000/information",
        "type": "curl"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "accesstoken",
            "description": "<p>Access token used to retrieve the thing&#39;s information. Should at be the read token.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"accesstoken\": \"12345\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "key-value type": [
          {
            "group": "key-value type",
            "type": "Object[]",
            "optional": false,
            "field": "information",
            "description": "<p>Example for key-value type. See example response for key-value for sample data.</p> "
          },
          {
            "group": "key-value type",
            "type": "String",
            "optional": false,
            "field": "information.title",
            "description": "<p>Set title to &quot;Status&quot;.</p> "
          },
          {
            "group": "key-value type",
            "type": "String",
            "optional": true,
            "field": "information.type",
            "description": "<p>Omitted in this example to force a key value type.</p> "
          },
          {
            "group": "key-value type",
            "type": "String",
            "optional": false,
            "field": "information.value",
            "description": "<p>Set raw value to &quot;working&quot;.</p> "
          }
        ],
        "boolean type": [
          {
            "group": "boolean type",
            "type": "Object[]",
            "optional": false,
            "field": "information",
            "description": "<p>Example for boolean type. See example response for boolean for sample data.</p> "
          },
          {
            "group": "boolean type",
            "type": "String",
            "optional": false,
            "field": "information.title",
            "description": "<p>Set title to &quot;Working&quot;.</p> "
          },
          {
            "group": "boolean type",
            "type": "String",
            "optional": false,
            "field": "information.type",
            "description": "<p>Set type to boolean. Swoty will render a green/red badge with the label &quot;yes&quot;/&quot;no&quot; for true/false values.</p> "
          },
          {
            "group": "boolean type",
            "type": "String",
            "optional": false,
            "field": "information.value",
            "description": "<p>Sets value to true so SWOTY will render a green badge with the label &quot;yes&quot;.</p> "
          }
        ],
        "table type": [
          {
            "group": "table type",
            "type": "Object[]",
            "optional": false,
            "field": "information",
            "description": "<p>Example for table type. See example response for table for sample data.</p> "
          },
          {
            "group": "table type",
            "type": "String",
            "optional": false,
            "field": "information.title",
            "description": "<p>Here we use aa bus station as an example which displays a table with bus lines and their arrival times.</p> "
          },
          {
            "group": "table type",
            "type": "String",
            "optional": false,
            "field": "information.type",
            "description": "<p>Use type &quot;table&quot; so SWOTY will display the data as a HTML table.</p> "
          },
          {
            "group": "table type",
            "type": "Object",
            "optional": false,
            "field": "information.value",
            "description": "<p>When using the table type, the value is not a primitive anymore but a complex JSON object containing the table headers and rows.</p> "
          },
          {
            "group": "table type",
            "type": "String[]",
            "optional": false,
            "field": "information.value.header",
            "description": "<p>An Array containing the html table headers. Here we will use &quot;Bus line&quot; and &quot;Arrival time&quot; as headers.</p> "
          },
          {
            "group": "table type",
            "type": "String[][]",
            "optional": false,
            "field": "information.value.data",
            "description": "<p>The table rows. This is an array containing arrays. Every nested array represents one table row. Here we define to arriving buses, line 11 arriving at 15:22 and line 5 arriving at 16:15.</p> "
          }
        ],
        "percentage type": [
          {
            "group": "percentage type",
            "type": "Object[]",
            "optional": false,
            "field": "information",
            "description": "<p>Example for percentage type. See example response for percentage for sample data.</p> "
          },
          {
            "group": "percentage type",
            "type": "String",
            "optional": false,
            "field": "information.title",
            "description": "<p>Example for a progress bar using percentage type.</p> "
          },
          {
            "group": "percentage type",
            "type": "String",
            "optional": false,
            "field": "information.type",
            "description": "<p>Use percentage type by providing &quot;percentage&quot; as type.</p> "
          },
          {
            "group": "percentage type",
            "type": "String",
            "optional": false,
            "field": "information.value",
            "description": "<p>Percent finished (progress) from 0 to 100.</p> "
          }
        ],
        "html type": [
          {
            "group": "html type",
            "type": "Object[]",
            "optional": false,
            "field": "information",
            "description": "<p>Example for html type. See example response for html for sample data.</p> "
          },
          {
            "group": "html type",
            "type": "String",
            "optional": false,
            "field": "information.title",
            "description": "<p>Title for the html content. SWOTY will show the title prior to the html formatted content.</p> "
          },
          {
            "group": "html type",
            "type": "String",
            "optional": false,
            "field": "information.type",
            "description": "<p>Use html type by providing &quot;html&quot; as type.</p> "
          },
          {
            "group": "html type",
            "type": "String",
            "optional": false,
            "field": "information.value",
            "description": "<p>Some static cotent with extra HTML markup.</p> "
          }
        ],
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Object[]",
            "optional": false,
            "field": "information",
            "description": "<p>List of information about this thing.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "information.title",
            "description": "<p>The title for the information. SWOTY will use this as title.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": true,
            "field": "information.type",
            "description": "<p>The type of data.  Currently supported:</p> <ul> <li>boolean</li> <li>table</li> <li>percentage</li> <li>html</li> <li>if no type is provided, SWOTY will display the raw value (escaped)</li> </ul> <p>See other example responses for detailed useage</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "(key-value tye) {json} Success-Response with boolean type:",
          "content": "HTTP/1.1 200 OK\n{\n    \"information\": [\n      {\n          \"title\" : \"Status\",\n          \"value\" : \"working\"\n      }\n     ]\n   }",
          "type": "json"
        },
        {
          "title": "(boolean tye) {json} Success-Response with boolean type:",
          "content": "HTTP/1.1 200 OK\n{\n    \"information\": [\n      {\n          \"title\": \"Working\",\n          \"type\": \"boolean\",\n          \"value\": true\n      }\n     ]\n   }",
          "type": "json"
        },
        {
          "title": "(table tye) {json} Success-Response with boolean type: ",
          "content": "HTTP/1.1 200 OK\n{\n    \"information\": [\n      {\n          \"title\" : \"arrivals\",\n          \"type\" : \"table\",\n          \"value\" : {\n              \"header\" : [\"Bus line\", \"Arrival time\"],\n              \"data\" : [\n                  [\"11\", \"15:22\"],\n                  [\"5\", \"16:15\"]\n              ]\n          }\n      }\n     ]\n   }",
          "type": "json"
        },
        {
          "title": "(percentage tye) {json} Success-Response with percentage type:",
          "content": "HTTP/1.1 200 OK\n{\n    \"information\": [\n      {\n          \"title\" : \"Progress making coffee\",\n          \"type\" : \"percentage\",\n          \"value\" : \"0\"\n      }\n     ]\n   }",
          "type": "json"
        },
        {
          "title": "(html tye) {json} Success-Response with html type:",
          "content": "HTTP/1.1 200 OK\n{\n    \"information\": [\n      {\n          \"title\" : \"value\",\n          \"type\" : \"html\",\n          \"value\" : \"<div><b>html value example</b></div>\"\n      }\n     ]\n   }",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"information\": [\n      {\n          \"title\" : \"Status\",\n          \"value\" : \"working\"\n      },\n\n      {\n          \"title\": \"Working\",\n          \"type\": \"boolean\",\n          \"value\": true\n      },\n\n      {\n          \"title\" : \"value\",\n          \"type\" : \"table\",\n          \"value\" : {\n              \"header\" : [\"header1\", \"header2\", \"header3\"],\n              \"data\" : [\n                  [\"value1\", \"value2\", \"value3\"],\n                  [\"value1\", \"value2\", \"value3\"]\n              ]\n          }\n      },\n\n      {\n          \"title\" : \"Progress making coffee\",\n          \"type\" : \"percentage\",\n          \"value\" : \"0\"\n      },\n\n      {\n          \"title\" : \"value\",\n          \"type\" : \"html\",\n          \"value\" : \"<div><b>html value example</b></div>\"\n      }\n     ]\n   }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "./app.js",
    "groupTitle": "API"
  },
  {
    "description": "<p>The route is used to register this thing in SWOTY. The URL .../register is just convention, the real URL is provided by the QR Code of this thing (which is scanned by the user / SWOTY). The the manufacturer of this thing is responsible himself that the URL on the QR code matches this route.</p> <p>The QR code should also provide a register token which will be sent with the register request. The register token acts like a serial key. It asserts, that the device may only be registered by the user who owns the thing (and thus the QR code).  A copy of the register token should be placed on software of this thing (maybe hardcoded on the filesystem) to check if the provided token is correct.</p> <p>Further more, this thing should mark the register token as used after the device has been registered so that no more people can register this thing. This token will uniquely identify this thing and authorize it for posting messages to SWOTY.</p> <p>SWOTY will append a so called network access token to the URL as a parameter. This token should be saved on the thing and sent with every request to SWOTY.</p> <p>As response, this route should provide SWOTY all relevant information needed to communicate with this thing. See example success response for more details.</p> ",
    "type": "get",
    "url": "/register",
    "title": "Register this thing in SWOTY network",
    "name": "register",
    "group": "API",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -i http://localhost:3000/register?access_token=12345&networktoken=13579",
        "type": "curl"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "access_token",
            "description": "<p>Accesstoken or register token.  This is the included token in the QR code. Once used, this token should not work anymore.</p> "
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "networktoken",
            "description": "<p>Accesstoken for this thing giving it permissions to POST to SWOTY.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "device",
            "description": "<p>Device information.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "device.id",
            "description": "<p>Internal id of the device.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "device.classification",
            "description": "<p>Classification of this thing. Currently not used by SWOTY.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "device.url",
            "description": "<p>Base (absolute) URL to the thing&#39;s api endpoint. All URLs like deregister, functions and information will be generated by SWOTY by convention.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": true,
            "field": "device.profileimage",
            "description": "<p>Optional image repreenting this thing. If no image is provided, SWOTY will use a default image.</p> "
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "device.tokens",
            "description": "<p>Object defining all available access tokens SWOTY will use to access this thing.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "device.tokens.owner_token",
            "description": "<p>Owner token. Used for administrative actions like deregister.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "device.tokens.write_token",
            "description": "<p>Write token. Used to activate functions.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "device.tokens.read_token",
            "description": "<p>Read token. Used to read information about this thing.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "  HTTP/1.1 200 OK\n  {\n    \"device\": {\n        \"id\": 12345,\n        \"classification\": \"sdf\",\n        \"url\": \"http://.....\",\n        \"profileimage\": \"http://....\",\n        \"tokens\": {\n            \"owner_token\": \"293487239\",\n            \"write_token\": \"2305982304\",\n            \"read_token\": \"23405834905\"\n        }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "./app.js",
    "groupTitle": "API"
  },
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p> "
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./apidoc/main.js",
    "group": "_Users_markus_GitHub_swot_minimum_apidoc_main_js",
    "groupTitle": "_Users_markus_GitHub_swot_minimum_apidoc_main_js",
    "name": ""
  },
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p> "
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./doc/main.js",
    "group": "_Users_markus_GitHub_swot_minimum_doc_main_js",
    "groupTitle": "_Users_markus_GitHub_swot_minimum_doc_main_js",
    "name": ""
  }
] });