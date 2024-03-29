var express = require('express');
var app = express();


/**
 * @apiDefine notAuthorized
 * @apiErrorExample {json} Error-Response: not authorized
 *     HTTP/1.1 401 OK
 *     {
 *       "message": "Access Token not valid"
 *     }
 */

/**
 * @apiDescription
 * The route is used to register this thing in SWOTY. The URL .../register is just convention, the real URL is provided by the QR Code of this thing
 * (which is scanned by the user / SWOTY). The the manufacturer of this thing is responsible himself that the URL on the QR code matches this route.
 *
 * The QR code should also provide a register token which will be sent with the register request. The register token acts like a serial key.
 * It asserts, that the device may only be registered by the user who owns the thing (and thus the QR code). 
 * A copy of the register token should be placed on software of this thing (maybe hardcoded on the filesystem) to check if the provided token is correct.
 *
 * Further more, this thing should mark the register token as used after the device has been registered so that no more people can register this thing.
 * This token will uniquely identify this thing and authorize it for posting messages to SWOTY.
 *
 * SWOTY will append a so called network access token to the URL as a parameter. This token should be saved on the thing and sent with every request to SWOTY.
 *
 * As response, this route should provide SWOTY all relevant information needed to communicate with this thing. See example success response for more details.
 * 
 * @api {get} /register Register this thing in SWOTY network
 * @apiName register
 * @apiGroup API
 * @apiExample {curl} Example usage:
 *     curl -i http://localhost:3000/register?access_token=12345&networktoken=13579
 *
 * @apiParam {String} access_token Accesstoken or register token. 
 * This is the included token in the QR code. Once used, this token should not work anymore.
 * @apiParam {String} networktoken Accesstoken for this thing giving it permissions to POST to SWOTY. 
 *
 * @apiSuccess {Object} device Device information.
 * @apiSuccess {String} device.id Internal id of the device.
 * @apiSuccess {String} device.classification Classification of this thing. Currently not used by SWOTY.
 * @apiSuccess {String} device.url Base (absolute) URL to the thing's api endpoint. All URLs like deregister, functions and information will be generated by SWOTY by convention.
 * @apiSuccess {String} [device.profileimage] Optional image repreenting this thing. If no image is provided, SWOTY will use a default image.
 * @apiSuccess {Object} device.tokens Object defining all available access tokens SWOTY will use to access this thing.
 * @apiSuccess {String} device.tokens.owner_token Owner token. Used for administrative actions like deregister.
 * @apiSuccess {String} device.tokens.write_token Write token. Used to activate functions.
 * @apiSuccess {String} device.tokens.read_token Read token. Used to read information about this thing.
 *
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "device": {
 *           "id": 12345,
 *           "classification": "sdf",
 *           "url": "http://.....",
 *           "profileimage": "http://....",
 *           "tokens": {
 *               "owner_token": "293487239",
 *               "write_token": "2305982304",
 *               "read_token": "23405834905"
 *           }
 *       }
 *   }
 *
 *  @apiUse notAuthorized
 *  @apiErrorExample {json} Error-Response: device already registered
 *     HTTP/1.1 423 OK
 *     {
 *       "message": "Device already registered"
 *     }
 */
app.get('/register', function (req, res) {
  res.json({
        "device": {
            "id": "12345",
            "classification": "sdf",
            "url": "http://.....",
            "profileimage": "http://....",
            "tokens": {
                "owner_token": "293487239",
                "write_token": "2305982304",
                "read_token": "23405834905"
            }
        }
    });
});

/**
 * @api {get} /deregister Deregister this thing
 * @apiName deregister
 * @apiGroup API
 * 
 * @apiDescription
 * Deregister Action.
 * SWOTY calls this route, when a user wants to remove this thing from his profile.
 * This may be in case he/she doesn't want this thing anymore or he/she wants to give it to another user.
 * The internal access token/register token should be marked as unused so that device to ready to be registered again.
 *
 * The owner token should be passed to activate this function.
 * The token is passed by request header "accesstoken".
 * 
 * @apiExample {curl} Example usage:
 *     curl --header "accesstoken: 12345" -i http://localhost:3000/deregister
 *
 * @apiHeader {String} accesstoken Access token to activate this function. Should be the owner token.
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "accesstoken": "12345"
 *     }
 *
 * @apiSuccess {String} [message] Success message.
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "message" : "Device deregistered."
 *     }
 *
 * @apiUse notAuthorized
 */
app.get('/deregister', function (req, res) {
  res.json({
 	"message" : "Device deregistered."
 	});
});

/**
 * @api {get} /functions List of available functions
 * @apiName functions
 * @apiGroup API
 * 
 * @apiDescription
 * This route should give a list of the available functions which can be activated by a REST call.
 * Every function contains:
 * - a unique name for identification
 * - an URL which is called to activate the function
 * - an indicator if this function is available
 * - a list of parameters
 *
 * Every parameters has
 * - a unique name
 * - a type which will determine the representation on SWOTY
 * - an indicator if the parameter is optional / required
 * - a list of constraints
 *
 * Every constraint has:
 * - a type
 * - an error message if the user input (on SWOTY) does not match the constraint.
 * This means: every input of the user will be validated server side by SWOTY before submitting the
 * data to the network!
 *
 * If the functions of this thing change anytime after registrations, SWOTY should be notified about that.
 * (See Example call to Swoty to notify a change in the function list)
 *
 * @apiExample {curl} Example call to Swoty to notify a change in the function list
 * curl -i --header "accesstoken: mynetworkaccesstoken" http://urltoswoty.com/api/v1/information/update
 *
 * @apiExample {curl} Call by SWOTY to retrieve function list
 * curl -i --header "accesstoken: 12345" http://localhost:3000/functions
 *
 * @apiHeader {String} accesstoken Access token to retrieve function list. Should be the read token.
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "accesstoken": "12345"
 *     }
 *
 * @apiSuccess {Object[]} functions List of available functions
 * @apiSuccess {String} .name Unique name of the function
 * @apiSuccess {String} .url Url used by SWOTY to activate this function (POST)
 * @apiSuccess {String} .available Indicates if this function may be called at the moment
 * @apiSuccess {Object[]} .parameters A List of parameters required to activate this function. Every parameter will be set by user input. Representation depends on the parameter type.
 * @apiSuccess {String} .parameters.name The name of the parameter. Will be used as the label text for the corresponding input. Should be unique withing the function. Other functions may vontain a parameter with the same name.
 * @apiSuccess {String} .parameters.type The type of the parameter. 
 * 
 * Currently supported:
 * - integer
 * - Choice
 * - text
 * 
 * @apiSuccess {String[]} [.parameters.choices] Required when using "Choice" as parameter type. Contains a list of predefined values / options. SWOTY will send the index of the selected choice with an activate function request.
 * @apiSuccess {Boolean} [.parameters.multiple=false] Only evaluated when using "Choice" as parameter type. Lets the user select multiple values from the choice list.
 * @apiSuccess {Boolean} [.parameters.expanded=false] Only evaluated when using "Choice" as parameter type. 
 * - false: representation will be a dropdown list
 * - true: representation will be a radio select list
 * Note: the represenattion will vary if multiple is set to true
 * 
 * @apiSuccess {Boolean} .parameters.required Indicates if the user must fill in this value. 
 * @apiSuccess {Boolean} [.parameters.readOnly=false] Indicates if the parameter value is editable. If true, only the default value will be displayed if provided.
 * @apiSuccess {String} [.parameters.defaultValue] The default value for this parameter.
 * 
 * @apiSuccess {Object[]} [.parameters.constraints] A list of contraints for this parameter. SWOTY will validate the form input server side by this list before sending it to this thing.
 * @apiSuccess {String} .parameters.constraints.type The type of this constraint. 
 * Currently supperted:
 * - NotNull
 * - NotBlank
 * - Date
 * - DateTime
 * - Choice
 * - Country
 * - GreatedThan
 * - LessThan
 * - Language
 * - Locale
 * - Time
 * 
 * @apiSuccess {String} .parameters.constraints.message This message will be shown to the user if his input does not validate.
 * 
 * @apiSuccessExample {json} Example Success-Response (tries to list all available configuration options):
 *     HTTP/1.1 200 OK
 *     {
 *     	"functions": [{
 *	        "name": "Function-A",
 *	        "url": "http://www.example.com",
 *	        "available": true,
 *	        "parameters": [
 *	            {
 *	                "name": "Param-A",
 *	                "type": "integer",
 *	                "required": true,
 *	                "constraints": [
 *	                    {
 *	                        "type": "NotNull",
 *	                        "message": "Param A may not be null"
 *	                    }
 *	                ]
 *	            },
 *	            {
 *	                "name": "Param-B",
 *	                "type": "Choice",
 *	                "choices": [
 *	                    1, 2, 3, 4, 5
 *	                ],
 *	                "required": false,
 *	                "constraints": [
 *	                    {
 *	                        "type": "NotBlank",
 *	                        "message": "Param B may not be blank"
 *	                    }
 *	                ]
 *	            },
 *	            {
 *	                "name": "Param-C",
 *	                "type": "Choice",
 *	                "multiple": true,
 *	                "expanded": false,
 *	                "choices": [
 *	                    1, 2, 3, 4, 5
 *	                ],
 *	                "required": false,
 *	                "constraints": [
 *	                    {
 *	                        "type": "NotBlank",
 *	                        "message": "Param C may not be blank"
 *	                    }
 *	                ]
 *	            },
 *	            {
 *	                "name": "Param-D",
 *	                "type": "Choice",
 *	                "multiple": true,
 *	                "expanded": true,
 *	                "choices": [
 *	                    1, 2, 3, 4, 5
 *	                ],
 *	                "required": false,
 *	                "constraints": [
 *	                    {
 *	                        "type": "NotBlank",
 *	                        "message": "Param D may not be blank"
 *	                    }
 *	                ]
 *	            }
 *	    ]}, {
 *	        "name": "Function-B",
 *	        "url": "http://www.example.com",
 *	        "available": true,
 *	        "parameters": [
 *	            {
 *	                "name": "Param-2A",
 *	                "type": "text",
 *	                "required": true,
 *	                "constraints": [
 *	                    {
 *	                        "type": "Locale",
 *	                        "message": "Param 2A must be a locale"
 *	                    }
 *	                ],
 *	                "defaultValue": "hallo"
 *	            },
 *	            {
 *	                "name": "Param-2B",
 *	                "type": "integer",
 *	                "required": false,
 *	                "readOnly": true,
 *	                "defaultValue": 20
 *	            }
 *	        ]
 *		}]
 *	}
 *
 * @apiUse notAuthorized
 */
app.get('/functions', function (req, res) {
  res.json({});
});

/**
 * @apiDescription 
 * Example route representing a fictional function of this thing. This url may mostly be arbitary, but it must match one of the URLs
 * provided in the /functions response. SWOTY posts to this route if the user wants to active this function.
 * At least the write token should be present.
 *
 * The response of this route is not important to SWOTY. If this things wants to notify SWOTY about changes like 
 * starting to do something or finished doing something,
 * it may post these information in form of status updates to SWOTY (see SWOTY REST API reference).
 *
 * @api {post} /functions/activateexamplefunction Activate a specific function
 * @apiName activate a function
 * @apiGroup API
 *
 * @apiExample {curl} Example usage:
 *     curl --header "accesstoken: 12345" -i http://localhost:3000/functions/activateexamplefunction
 *
 * @apiHeader {String} accesstoken Access token to activate this function. Should at least be the write token.
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "accesstoken": "12345"
 *     }
 *
 * @apiSuccess {String} [message] Success message.
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "message" : "Function activated."
 *     }
 *
 * @apiUse notAuthorized
 */
app.post('/functions/activateexamplefunction', function (req, res) {
  res.json({});
});

/**
 * @apiDescription 
 * Returns a list of information about this thing. These information should represent the current status.
 * If the status changes, SWOTY should be notified about that. To do that, just make a POST request to SWOTY,
 * the network will then retrieve the updated information from this route,
 * See SWOTY REST Api documentation for more details.
 *
 * @api {get} /functions/information Information about this thing and it's status.
 * @apiName information
 * @apiGroup API
 *
 * @apiExample {curl} Example usage:
 *     curl --header "accesstoken: 12345" -i http://localhost:3000/information
 *
 * @apiHeader {String} accesstoken Access token used to retrieve the thing's information. Should at be the read token.
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "accesstoken": "12345"
 *     }
 * 
 * @apiSuccess (key-value type) {Object[]} information Example for key-value type. See example response for key-value for sample data.
 * @apiSuccess (key-value type) {String} information.title Set title to "Status".
 * @apiSuccess (key-value type) {String} [information.type] Omitted in this example to force a key value type. 
 * @apiSuccess (key-value type) {String} information.value Set raw value to "working".
 * @apiSuccessExample (key-value tye) {json} Success-Response with boolean type:
 *     HTTP/1.1 200 OK
 *     {
 *         "information": [
 *           {
 *               "title" : "Status",
 *               "value" : "working"
 *           }
 *          ]
 *        }
 *
 * @apiSuccess (boolean type) {Object[]} information Example for boolean type. See example response for boolean for sample data.
 * @apiSuccess (boolean type) {String} information.title Set title to "Working".
 * @apiSuccess (boolean type) {String} information.type Set type to boolean. Swoty will render a green/red badge with the label "yes"/"no" for true/false values.
 * @apiSuccess (boolean type) {String} information.value Sets value to true so SWOTY will render a green badge with the label "yes".
 * @apiSuccessExample (boolean tye) {json} Success-Response with boolean type:
 *     HTTP/1.1 200 OK
 *     {
 *         "information": [
 *           {
 *               "title": "Working",
 *               "type": "boolean",
 *               "value": true
 *           }
 *          ]
 *        }
 *
 * @apiSuccess (table type) {Object[]} information Example for table type. See example response for table for sample data.
 * @apiSuccess (table type) {String} information.title Here we use aa bus station as an example which displays a table with bus lines and their arrival times.
 * @apiSuccess (table type) {String} information.type Use type "table" so SWOTY will display the data as a HTML table.
 * @apiSuccess (table type) {Object} information.value When using the table type, the value is not a primitive anymore but a complex JSON object containing the table headers and rows.
 * @apiSuccess (table type) {String[]} information.value.header An Array containing the html table headers. Here we will use "Bus line" and "Arrival time" as headers.
 * @apiSuccess (table type) {String[][]} information.value.data The table rows. This is an array containing arrays. Every nested array represents one table row. Here we define to arriving buses, line 11 arriving at 15:22 and line 5 arriving at 16:15.
 * @apiSuccessExample (table tye) {json} Success-Response with boolean type: 
 *     HTTP/1.1 200 OK
 *     {
 *         "information": [
 *           {
 *               "title" : "arrivals",
 *               "type" : "table",
 *               "value" : {
 *                   "header" : ["Bus line", "Arrival time"],
 *                   "data" : [
 *                       ["11", "15:22"],
 *                       ["5", "16:15"]
 *                   ]
 *               }
 *           }
 *          ]
 *        }
 *
 * @apiSuccess (percentage type) {Object[]} information Example for percentage type. See example response for percentage for sample data.
 * @apiSuccess (percentage type) {String} information.title Example for a progress bar using percentage type.
 * @apiSuccess (percentage type) {String} information.type Use percentage type by providing "percentage" as type.
 * @apiSuccess (percentage type) {String} information.value Percent finished (progress) from 0 to 100.
 * @apiSuccessExample (percentage tye) {json} Success-Response with percentage type:
 *     HTTP/1.1 200 OK
 *     {
 *         "information": [
 *           {
 *               "title" : "Progress making coffee",
 *               "type" : "percentage",
 *               "value" : "0"
 *           }
 *          ]
 *        }
 *
 * @apiSuccess (html type) {Object[]} information Example for html type. See example response for html for sample data.
 * @apiSuccess (html type) {String} information.title Title for the html content. SWOTY will show the title prior to the html formatted content.
 * @apiSuccess (html type) {String} information.type Use html type by providing "html" as type.
 * @apiSuccess (html type) {String} information.value Some static cotent with extra HTML markup.
 * @apiSuccessExample (html tye) {json} Success-Response with html type:
 *     HTTP/1.1 200 OK
 *     {
 *         "information": [
 *           {
 *               "title" : "value",
 *               "type" : "html",
 *               "value" : "<div><b>html value example</b></div>"
 *           }
 *          ]
 *        }
 *
 * @apiSuccess {Object[]} information List of information about this thing.
 * @apiSuccess {String} information.title The title for the information. SWOTY will use this as title.
 * @apiSuccess {String} [information.type] The type of data. 
 * Currently supported:
 * - boolean
 * - table
 * - percentage
 * - html
 * - if no type is provided, SWOTY will display the raw value (escaped)
 *
 * See other example responses for detailed useage
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "information": [
 *           {
 *               "title" : "Status",
 *               "value" : "working"
 *           },
 *
 *           {
 *               "title": "Working",
 *               "type": "boolean",
 *               "value": true
 *           },
 *
 *           {
 *               "title" : "value",
 *               "type" : "table",
 *               "value" : {
 *                   "header" : ["header1", "header2", "header3"],
 *                   "data" : [
 *                       ["value1", "value2", "value3"],
 *                       ["value1", "value2", "value3"]
 *                   ]
 *               }
 *           },
 *
 *           {
 *               "title" : "Progress making coffee",
 *               "type" : "percentage",
 *               "value" : "0"
 *           },
 *
 *           {
 *               "title" : "value",
 *               "type" : "html",
 *               "value" : "<div><b>html value example</b></div>"
 *           }
 *          ]
 *        }
 *
 * @apiUse notAuthorized
 */
app.get('/information', function (req, res) {
  res.json({});
});

var server = app.listen(4000, function () {

  var host = server.address().address;
  var port = server.address().port;
});